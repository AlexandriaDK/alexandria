    google.load("language", "1");
    google.load("jquery", "1");

    function initialize() {
		$("p").hover(translateme);
		$("td").hover(translateme);
		$("h3").hover(translateme);
		$("li").hover(translateme);
		$("div.leftmenucontent").hover(translateme);
	}

	function getRandomLanguage() {
		var languages = ["en", "de", "es", "sv", "no", "ga", "fr", "ar", "zh-CN", "is", "sw", "cy", "yi", "iw", "tr", "vi", "ru", "ja", "sr", "fa", "pl", "pt", "id", "hi", "af", "nl", "ca" ];
		return languages[Math.floor(Math.random()*languages.length)];
	}

	function translateme() {
		if ($(this).attr("lang")) { // only translate once; skip if lang attribute is present
			return;
		}
		var htmlthis = $(this); // scope; for access of object inside translate function
		var text = $(this).html();
		var randomLanguage = getRandomLanguage();
		$(this).attr("lang",randomLanguage ); // set lang attribute to prevent further translations

		google.language.detect(text, function(result) {
			if (!result.error && result.language) {
				google.language.translate(text, result.language, randomLanguage, function(result) {
					var translated = document.getElementById("translation");
					if (result.translation) {
						htmlthis.html(result.translation);
					}
				});
			}
		});
	}

    google.setOnLoadCallback(initialize);
