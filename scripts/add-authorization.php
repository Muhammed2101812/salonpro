<?php

/**
 * Script to add authorization checks to API controllers
 *
 * This script automatically adds $this->authorize() calls to controller methods
 * following the pattern used in CustomerController, AppointmentController, and EmployeeController.
 *
 * Usage: php scripts/add-authorization.php [controller-name]
 * Example: php scripts/add-authorization.php ProductController
 *
 * Or run for all controllers:
 * php scripts/add-authorization.php --all
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

// Define the controllers directory
$controllersDir = __DIR__ . '/../app/Http/Controllers/API';

// Get command line arguments
$controller = $argv[1] ?? null;

if (!$controller) {
    echo "Usage: php add-authorization.php [ControllerName] or --all\n";
    echo "Example: php add-authorization.php ProductController\n";
    echo "Or: php add-authorization.php --all\n";
    exit(1);
}

// Process all controllers or specific one
if ($controller === '--all') {
    $files = glob($controllersDir . '/*Controller.php');

    // Skip already processed controllers
    $skipControllers = [
        'BaseController.php',
        'CustomerController.php',
        'AppointmentController.php',
        'EmployeeController.php',
    ];

    $processed = 0;
    $skipped = 0;

    foreach ($files as $file) {
        $filename = basename($file);

        if (in_array($filename, $skipControllers)) {
            echo "í  Skipping {$filename} (already processed or base controller)\n";
            $skipped++;
            continue;
        }

        if (processController($file)) {
            echo " Processed {$filename}\n";
            $processed++;
        } else {
            echo "   Could not process {$filename}\n";
        }
    }

    echo "\n=Ê Summary:\n";
    echo "   Processed: {$processed}\n";
    echo "   Skipped: {$skipped}\n";
    echo "   Total: " . ($processed + $skipped) . "\n";
} else {
    // Process single controller
    $file = $controllersDir . '/' . $controller . '.php';

    if (!file_exists($file)) {
        echo "L Controller not found: {$file}\n";
        exit(1);
    }

    if (processController($file)) {
        echo " Successfully processed {$controller}\n";
    } else {
        echo "L Failed to process {$controller}\n";
        exit(1);
    }
}

/**
 * Process a single controller file
 */
function processController(string $filePath): bool
{
    $content = file_get_contents($filePath);

    if (!$content) {
        return false;
    }

    // Extract model name from controller name
    $controllerName = basename($filePath, '.php');
    $modelName = str_replace('Controller', '', $controllerName);

    // Check if model import already exists
    if (strpos($content, "use App\\Models\\{$modelName};") !== false) {
        echo "   9  Model already imported, checking methods...\n";
    } else {
        // Add model import after other use statements
        $content = addModelImport($content, $modelName);
    }

    // Add authorization to methods
    $content = addAuthorizationToIndex($content, $modelName);
    $content = addAuthorizationToStore($content, $modelName);
    $content = addAuthorizationToShow($content, $modelName);
    $content = addAuthorizationToUpdate($content, $modelName);
    $content = addAuthorizationToDestroy($content, $modelName);
    $content = addAuthorizationToRestore($content, $modelName);
    $content = addAuthorizationToForceDestroy($content, $modelName);

    // Write back to file
    return file_put_contents($filePath, $content) !== false;
}

/**
 * Add model import statement
 */
function addModelImport(string $content, string $modelName): string
{
    // Find the last use statement in the namespace
    $pattern = '/(use App\\\\Http\\\\.*?;\n)(use Illuminate)/s';

    if (preg_match($pattern, $content)) {
        $replacement = "$1use App\\Models\\{$modelName};\n$2";
        $content = preg_replace($pattern, $replacement, $content);
    } else {
        // Fallback: add after namespace declaration
        $pattern = '/(namespace App\\\\Http\\\\Controllers\\\\API;\n\n)/';
        $replacement = "$1use App\\Models\\{$modelName};\n";
        $content = preg_replace($pattern, $replacement, $content);
    }

    return $content;
}

/**
 * Add authorization to index method
 */
function addAuthorizationToIndex(string $content, string $modelName): string
{
    // Check if authorization already exists
    if (preg_match('/public function index.*?{.*?authorize.*?viewAny/s', $content)) {
        return $content;
    }

    // Pattern: public function index(...) { [content]
    $pattern = '/(public function index\([^)]*\)[^{]*\{)(\s*)/';
    $replacement = "$1\n        \$this->authorize('viewAny', {$modelName}::class);\n$2";

    return preg_replace($pattern, $replacement, $content, 1);
}

/**
 * Add authorization to store method
 */
function addAuthorizationToStore(string $content, string $modelName): string
{
    // Check if authorization already exists
    if (preg_match('/public function store.*?{.*?authorize.*?create/s', $content)) {
        return $content;
    }

    $pattern = '/(public function store\([^)]*\)[^{]*\{)(\s*)/';
    $replacement = "$1\n        \$this->authorize('create', {$modelName}::class);\n$2";

    return preg_replace($pattern, $replacement, $content, 1);
}

/**
 * Add authorization to show method
 */
function addAuthorizationToShow(string $content, string $modelName): string
{
    // Check if authorization already exists
    if (preg_match('/public function show.*?{.*?authorize.*?view/s', $content)) {
        return $content;
    }

    // Find the line where model is fetched
    $varName = strtolower($modelName);

    // Pattern: fetch model line, then add authorization after it
    $pattern = '/(\$' . $varName . ' = \$this->[^;]+;)(\s*\n)/';
    $replacement = "$1\n\n        \$this->authorize('view', \${$varName});\n$2";

    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $replacement, $content, 1);
    }

    return $content;
}

/**
 * Add authorization to update method
 */
function addAuthorizationToUpdate(string $content, string $modelName): string
{
    // Check if authorization already exists
    if (preg_match('/public function update.*?{.*?authorize.*?update/s', $content)) {
        return $content;
    }

    $varName = strtolower($modelName);

    // Two patterns: either model is fetched first, or we need to add fetch + authorize

    // Pattern 1: Model already fetched
    $pattern1 = '/(public function update[^{]*\{[^}]*?\$' . $varName . ' = \$this->[^;]+;)(\s*\n)/';
    $replacement1 = "$1\n\n        \$this->authorize('update', \${$varName});\n$2";

    if (preg_match($pattern1, $content)) {
        return preg_replace($pattern1, $replacement1, $content, 1);
    }

    // Pattern 2: Model not fetched, need to add fetch before service call
    $pattern2 = '/(public function update\([^)]*\$id[^)]*\)[^{]*\{)(\s*\n)(\s*)(\$' . $varName . ' = \$this->' . $varName . 'Service->update)/';
    $replacement2 = "$1$2$3\${$varName} = \$this->{$varName}Service->findByIdOrFail(\$id);\n\n$3\$this->authorize('update', \${$varName});\n\n$3$4";

    if (preg_match($pattern2, $content)) {
        return preg_replace($pattern2, $replacement2, $content, 1);
    }

    return $content;
}

/**
 * Add authorization to destroy method
 */
function addAuthorizationToDestroy(string $content, string $modelName): string
{
    // Check if authorization already exists
    if (preg_match('/public function destroy.*?{.*?authorize.*?delete/s', $content)) {
        return $content;
    }

    $varName = strtolower($modelName);

    // Pattern: add fetch + authorize before service call
    $pattern = '/(public function destroy\([^)]*\$id[^)]*\)[^{]*\{)(\s*\n)(\s*)(\$this->' . $varName . 'Service->delete)/';
    $replacement = "$1$2$3\${$varName} = \$this->{$varName}Service->findByIdOrFail(\$id);\n\n$3\$this->authorize('delete', \${$varName});\n\n$3$4";

    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $replacement, $content, 1);
    }

    return $content;
}

/**
 * Add authorization to restore method
 */
function addAuthorizationToRestore(string $content, string $modelName): string
{
    // Check if restore method exists
    if (!preg_match('/public function restore/', $content)) {
        return $content;
    }

    // Check if authorization already exists
    if (preg_match('/public function restore.*?{.*?authorize.*?restore/s', $content)) {
        return $content;
    }

    $varName = strtolower($modelName);

    $pattern = '/(public function restore\([^)]*\$id[^)]*\)[^{]*\{)(\s*\n)(\s*)(\$this->' . $varName . 'Service->restore)/';
    $replacement = "$1$2$3\${$varName} = \$this->{$varName}Service->findByIdOrFail(\$id);\n\n$3\$this->authorize('restore', \${$varName});\n\n$3$4";

    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $replacement, $content, 1);
    }

    return $content;
}

/**
 * Add authorization to forceDestroy method
 */
function addAuthorizationToForceDestroy(string $content, string $modelName): string
{
    // Check if forceDestroy method exists
    if (!preg_match('/public function forceDestroy/', $content)) {
        return $content;
    }

    // Check if authorization already exists
    if (preg_match('/public function forceDestroy.*?{.*?authorize.*?forceDelete/s', $content)) {
        return $content;
    }

    $varName = strtolower($modelName);

    $pattern = '/(public function forceDestroy\([^)]*\$id[^)]*\)[^{]*\{)(\s*\n)(\s*)(\$this->' . $varName . 'Service->forceDelete)/';
    $replacement = "$1$2$3\${$varName} = \$this->{$varName}Service->findByIdOrFail(\$id);\n\n$3\$this->authorize('forceDelete', \${$varName});\n\n$3$4";

    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $replacement, $content, 1);
    }

    return $content;
}

echo "\n Script completed!\n";
