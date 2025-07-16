# pmScriptPlugins

## ✅ How to Install pmScriptPlugins (Visual Explanation)

```
📁 PocketMine-MP/
│
├── 📁 plugins/
│   ├── 📄 CpsHandler.php                ←✅ Place directly here
│   ├── 📄 EnableVibrantVisualsPlugin.php←✅ Same here
│   └── 📄 TransferDoor.php              ←✅ Same here
│
├── 📁 worlds/
├── 📄 PocketMine-MP.phar
└── ...
```

### 🔽 Steps

1. Copy all `*.php` files directly under the `plugins/` folder.
2. When you start PocketMine, they will be automatically loaded.


## 💡 This is the “ScriptPlugin” Style

Unlike traditional PMMP plugins:

- No `plugin.yml` or autoload settings are required.
- Each PHP file works standalone, as long as it extends `PluginBase`.
- A PHPDoc header like the following may be necessary:

```php
/**
 * @name EnableVibrantVisualsPlugin
 * @api 5.30.0
 * @description Let's enable Vibrant Visuals!
 * @version 1.0.0
 * @main EnableVibrantVisualsPlugin
 */
```
