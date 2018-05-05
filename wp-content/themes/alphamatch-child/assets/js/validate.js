jQuery(document).ready(function($) {
    'use strict';

function AppModel(attrs, elem){
    this.val = '';
    this.elem = elem;
 
    this.listeners = {
        valid: [],
        invalid: []
    };
 
    this.attrs = {
        required: '',
        maxlength: 8,
        minlength: 4,
        coupon: 'ALPHA18',
        number: /^[a-zA-Z0-9”#$%&*+-./?@_]*$/,
        url: /^(https?|ftp)(:\/\/[-_.!~*¥'()a-zA-Z0-9;¥/?:¥@&=+¥$,%#]+)$/,
        mail: /^[a-zA-Z0-9."!#$%&*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
    };
 
    this.message = {
        required: 'Required',
        maxlength: this.attrs.maxlength + 'Maximum 8 characters',
        minlength: this.attrs.minlength + 'Minimum 4 characters',
        number: 'Numbers only',
        coupon: 'Invalid Coupon.',
        coupon1: 'Invalid Coupon.',
        coupon2: 'Invalid Coupon.',        
        url: 'Must be a properly formmated url',
        mail: 'Must be a properly formmated email address'
    };
 
}
 
AppModel.prototype.set = function(val){
    this.val = val;
    this.validate();
};
 
AppModel.prototype.validate = function(){
    var val;
 
    // バリデーションでエラーが出たものを保存しておく配列を用意
    this.errors = [];
 
    for(var key in this.attrs){
        val = this.attrs[key];
        if(this[key](val)) return;
    }
 
    this.trigger(!this.errors.length ? 'valid' : 'invalid');
};
 
AppModel.prototype.required = function(){
    var key = 'required';
 
    if(this.elem.hasClass('js-validate-' + key)){
        if(this.val ===''){
            this.errors.push(key);
        }
    }
};

AppModel.prototype.coupon = function(rule){
    var key = 'coupon';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(!this.val.match(rule)){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.maxlength = function(rule){
    var key = 'maxlength';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(rule < this.val.length){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.minlength = function(rule){
    var key = 'minlength';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(rule >= this.val.length){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.number = function(rule){
    var key = 'number';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(!this.val.match(rule)){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.url = function(rule){
    var key = 'url';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(!this.val.match(rule)){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.mail = function(rule){
    var key = 'mail';
 
    if(this.val === '') return;
 
    if(this.elem.hasClass('js-validate-' + key)){
 
        if(!this.val.match(rule)){
            this.errors.push(key);
        }
    }
};
 
AppModel.prototype.on = function(event, func) {
    this.listeners[event].push(func);
};
 
AppModel.prototype.trigger = function(event){
    $.each(this.listeners[event], function(){
        this();
    });
};
 
function AppView (el){
 
    // 初期化
    this.initialize(el);
 
    // イベント
    this.handleEvents();
}
 
AppView.prototype.initialize = function(el){
    this.$el = $(el);
    this.$list = this.$el.next();
    this.$submit = $('.js-submit');
    this.$form = $('.js-form');
    var obj = this.$el.data();
 
    if(this.$el.hasClass('required')){
        obj["required"] = "";
    }
 
 
    this.model = new AppModel(obj, this.$el);
 
};
 
AppView.prototype.handleEvents = function(){
    var self = this;
    this.$el.on('keyup', function(e){
        self.onKeyup(e);
        console.log(self);
    });
 
    this.$submit.on('click', function(){
        self.onClick();
    });
 
    this.model.on('valid', function(){
        self.onValid();
    });
 
    this.model.on('invalid', function(){
        self.onInvalid();
    });
};
 
AppView.prototype.onKeyup = function(e){
    var $target = $(e.currentTarget);
    this.model.set($target.val());
};
 
AppView.prototype.onClick = function(){
    var $target = this.$el;
 
    if($target.hasClass('js-validate-required')){
        this.model.set($target.val());
    }
 
};
 
AppView.prototype.onValid = function(){
    var $targetForm = this.$form;
 
    this.$el.removeClass('error');
    this.$list.find('.errText').remove();

    $('.payment-process').removeClass('disabled');

    this.$submit.click(function(){
        $targetForm.submit();
    });
};
 
AppView.prototype.onInvalid = function(){
 
    var self = this;
    self.message = this.model.message;
 
    this.$el.addClass('error');
    this.$list.find('.errText').remove();
 
    $.each(this.model.errors, function(index, val){
        var elem = self.message[val];
        self.$list.append('<p class="errText">' + elem + '</p>');
    });

    $('.payment-process').addClass('disabled');
};
 
$('.js-validate').each(function(){
    new AppView(this);
});

});