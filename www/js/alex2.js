function onLoad() {
	$("p").hover(randEffect);
	$("td").hover(randEffect);
	$("li").hover(randEffect);
	$("a").hover(randEffect);
}

function randEffect() {
	var rand_no = Math.ceil(24*Math.random() );
		if (rand_no == 1) {
			$(this).slideUp().show("slow");
		} else if (rand_no == 2) {
			$(this).fadeOut().show("slow");
		} else if (rand_no == 3) {
			$(this).slideDown().show("slow");
		} else if (rand_no == 4) {
			$(this).animate( {fontSize: "100px"}, 1000);
		} else if (rand_no == 5) {
			$(this).animate( {fontSize: "30px"}, 1000);
		} else if (rand_no == 6) {
			$(this).animate( {fontSize: "4px"}, 1000);
		} else if (rand_no == 7) {
			$(this).animate( {"opacity": 0.8} , 1000);
		} else if (rand_no == 8) {
			$(this).animate( {"opacity": 0.3} , 1000);
		} else if (rand_no == 9) {
			$(this).css( {"color": "red"} );
		} else if (rand_no == 10) {
			$(this).css( {"font-weight": "bold"} );
		} else if (rand_no == 11) {
			$(this).css( {"color": "yellow"} );
		} else if (rand_no == 12) {
			$(this).css( {"background-color": "green"} );
		} else if (rand_no == 13) {
			$(this).css( {"background-color": "blue"} );
		} else if (rand_no == 14) {
			$(this).css( {"background-color": "black"} );
			$(this).css( {"color": "white"} );
		} else if (rand_no == 15) {
			$(this).css( {"color": "white"} );
			$(this).css( {"background-color": "red"} );
		} else if (rand_no == 16) {
			$(this).animate( {padding: "+= 20px"} , "slow");
		} else if (rand_no == 17) {
			$(this).animate( {border: "9px dashed brown"} , "normal").
				animate( {border: "5px dotted #f80"} , "slow");
		} else if (rand_no == 18) {
			$(this).animate( {width: "50%"} , "normal").
				animate( {width: "80%"} , "normal").
				animate( {width: "30%"} , "slow");
		} else if (rand_no == 19) {
			$(this).animate( {fontSize: "10px"} ).
				animate( {fontSize: "50px"} ).
				animate( {marginLeft: "30px"} ).
				animate( {fontSize: "20px"});
		} else if (rand_no == 20) {
			$(this).hide("slow").show("slow");
		} else if (rand_no == 21) {
			$(this).css( {"text-align": "right"; } );
		} else if (rand_no == 22) {
			$(this).css( {"text-align": "left"; } );
		} else if (rand_no == 23) {
			$(this).css( {"direction": "ltr"; } );
		} else if (rand_no == 24) {
			$(this).css( {"direction": "rtl"; } );
		}

}
