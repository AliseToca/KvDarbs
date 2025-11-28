import Controller from '@controllers/controller';

class LanguagePageController extends Controller {
    static onStart() {
        return super.onStart();
    }
    init() {
        console.log('Language page controller initialized.');
    }
}

LanguagePageController.onStart();

export default LanguagePageController;
