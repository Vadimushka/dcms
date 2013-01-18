// include "core/dcms.js"

DCMS.Animate = function(duration, callback, delta){
    
    if (delta && DCMS.Animate.deltaFunctions[delta])
        this.delta = DCMS.Animate.deltaFunctions[delta];    
    else 
        this.delta = DCMS.Animate.deltaFunctions['life'];
        
    if (duration == undefined)
        duration = 1000;     
    
    if (typeof callback != 'function')
        throw new Error('callback не является функцией');
    else
        this.callback = callback;
    
    this.step = this.start = 0;
    this.end = 1;    
    this.step_length = (this.end - this.start) / (duration / 13);    
    this.timeInterval = setInterval(DCMS.getEventHandler(this.Step, this), 13);
};

    DCMS.Animate.prototype.IsEnded = function(){
        return this.step >= this.end;
    };

    DCMS.Animate.prototype.Step = function(){
        this.step += this.step_length;

        if (this.IsEnded()){
            this.End();
            return;
        }
        this.callback(this.delta(this.step));
    };

    DCMS.Animate.prototype.End = function(to_end_step){       
    
        if (to_end_step === undefined)
            to_end_step = true;

        this.step = this.end;
    
        if (this.timeInterval)
            clearInterval(this.timeInterval);
    
        if (to_end_step)
            this.callback(this.step);    
    };

    DCMS.Animate.deltaFunctions = {
        linear: function (input){
            return input;
        },
        drop: function(input){
            return DCMS.Animate.deltaFunctions._easeOut(DCMS.Animate.deltaFunctions._bounce)(input);        
        },
        life: function(input){
            return DCMS.Animate.deltaFunctions._easeOut(DCMS.Animate.deltaFunctions._quad)(input);
        },
        _bounce: function (progress) {
            for (var a = 0, b = 1, result; 1; a += b, b /= 2) {
                if (progress >= (7 - 4 * a) / 11) {
                    return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
                }
            }
            return 1;
        },
        _easeOut: function (delta) {
            return function(progress) {
                return 1 - delta(1 - progress);
            }
        },
        _quad: function (progress) {
            return Math.pow(progress, 4);
        }

    };