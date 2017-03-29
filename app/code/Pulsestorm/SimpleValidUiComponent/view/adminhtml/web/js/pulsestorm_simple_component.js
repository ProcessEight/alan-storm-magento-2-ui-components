define(['uiElement','ko'], function(Element, ko){
    viewModelConstructor = Element.extend({
        defaults: {
            template: 'Pulsestorm_SimpleValidUiComponent/pulsestorm_simple_template'
        }
    });

    return viewModelConstructor;
});