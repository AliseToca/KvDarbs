class Controller {
    static onStart() {
        const instance = new this();
        if (instance && typeof instance['init'] === 'function') {
            instance['init']();
        }
        return instance;
    }
}

export default Controller;
