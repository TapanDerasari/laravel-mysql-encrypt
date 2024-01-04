
## Example of existing data encryption

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TapanDerasari\MysqlEncrypt\Scopes\DecryptSelectScope;


class EncryptionForExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encryption-for-existing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encryption for existing data based on passed model with encryptable fields';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {      

        // Put existing models here in which you want to apply encryption
        $modelList = ['User'];
        foreach ($modelList as $model) {
            $modelName = 'App\\Models\\' . $model;

            // Check if the model exists
            if (!class_exists($modelName)) {
                $this->error("Model '{$modelName}' not found!");

                return;
            }

            // Instantiate the model
            $modelObj = new $modelName;
            $modelData = $modelObj->withoutGlobalScopes([DecryptSelectScope::class])->get();

            foreach ($modelData as $modelRecord) {
                $updateModel = $modelName::findOrFail($modelRecord->id);
                $fields = $updateModel->encryptable;
                foreach ($fields as $field) {
                    $updateModel->$field = $modelRecord->$field;
                }
                $updateModel->save();
            }
        }            
        
    }
}
```
