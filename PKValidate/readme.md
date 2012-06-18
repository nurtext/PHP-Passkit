# PHP PKValidate class for iOS 6 Passbook
This class provides the functionality to validate any errors in an pass.json file, to be able to catch them before serving the pass to the user or for easier debugging. 

The validation is probably far from complete, but I'm working on adding new errors. If you've got any suggestions, let me know, or add them yourself if you would like to.

## Dependencies
* PHP 5

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

Please note that iOS 6 and Passbook are still in beta, which means that the API can change at any moment. As new beta releases of iOS 6 come up, I'll try to update the class as soon as possible and add more features.

## Support
For info, mail me at tom@tomttb.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).