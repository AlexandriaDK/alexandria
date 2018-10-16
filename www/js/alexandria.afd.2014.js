$.fn.animateRotate = function(angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
        args.step = function(now) {
            $.style(e, 'transform', 'rotate(' + now + 'deg)');
            if (step) return step.apply(this, arguments);
        };

        $({deg: 0}).animate({deg: angle}, args);
    });
};

$.fn.animateSkew = function(angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
        args.step = function(now) {
            $.style(e, 'transform', 'skew(' + now + 'deg, ' + now + 'deg)');
            if (step) return step.apply(this, arguments);
        };

        $({deg: 0}).animate({deg: angle}, args);
    });
};

$.fn.animateRotateX = function(angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
        args.step = function(now) {
//            $.style(e, 'transform', 'rotateX(' + now + 'deg)');
            $.style(e, 'transform', 'rotateX(' + now + 'deg)');
            if (step) return step.apply(this, arguments);
        };

        $({deg: 0}).animate({deg: angle}, args);
    });
};

$.fn.animateRotateY = function(angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
        args.step = function(now) {
//            $.style(e, 'transform', 'rotateX(' + now + 'deg)');
            $.style(e, 'transform', 'rotateY(' + now + 'deg)');
            if (step) return step.apply(this, arguments);
        };

        $({deg: 0}).animate({deg: angle}, args);
    });
};

function onLoad() {
	window.setTimeout('randEffect()',5000);
}

function randEffect() {
	var effect = Math.ceil(3 * Math.random() );
	if (effect == 1) {
		var rand_no = Math.ceil(90*Math.random() - 45 );
		$('body').animateRotate(rand_no, 10000);
		$('body').animate( {
			 'margin-left': (rand_no * 10 ) + 'px',
			 'margin-top': (rand_no * 10 ) + 'px',
		}, 20000);
	} else if (effect == 2) {
		var rand_no = Math.ceil(160*Math.random() - 80 );
		$('body').animateSkew(rand_no, 10000);
	} else if (effect == 3) {
		var rand_no = Math.ceil(1000*Math.random() );
		$('body').animateRotateX(rand_no, 10000);
	}
}
