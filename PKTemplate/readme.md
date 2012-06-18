# PHP PKTemplate class for iOS 6 Passbook
This class provides the functionality to create and manage templates for passes and set values of fields, add locations and change serial numbers.

## Dependencies
* PHP 5
* PKLog.php class (included in the `shared` folder)

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

Please note that iOS 6 and Passbook are still in beta, which means that the API can change at any moment. As new beta releases of iOS 6 come up, I'll try to update the class as soon as possible and add more features.

### How to create Pass templates:
* Create a new folder.
* Add your assets (i.e. icon.png and background.png) to the folder. Don't use any subfolders, as they won't be added to the pass.
* Add your `pass.json` file to the folder. The values you set for fields in the pass.json file will be used as default values.
* Initialize the PHP class with the new template and start adding values:

```
$template = new PKTemplate('/path/to/template/folder');
```
**Note**: don't use a slash (`/`) at the end of the template folder path.

## Support
For info, mail me at tom@tomttb.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).