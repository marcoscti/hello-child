
$('.dropdown-hoverable').hover(function () {
	if (!$(this).hasClass("show"))
		$(this).children('[data-toggle="dropdown"]').click(), 100;
}, function () {
	if ($(this).hasClass("show"))
		$(this).children('[data-toggle="dropdown"]').click(), 100;
});

$('.popper').popover({
	placement: 'bottom',
	container: 'body',
	html: true,
	content: function () {
		return $(this).next('.popper-content').html();
	}
});

let toshow = $('.center').find('li').length;
toshow = toshow > 5 ? 5 : toshow;

$('.center').slick({
	centerMode: true,
	infinite: true,
	slidesToShow: toshow,
	speed: 500,
	variableWidth: false,
});
$(".slick-current").removeClass("slick-current");
$('.center').on('beforeChange', function (event, slick, currentSlide, nextSlide) {
	console.log('beforeChange', currentSlide, nextSlide);
});

$("[data-slick-index]").click(function () {
	$('.center').slick("slickGoTo", $(this).attr("data-slick-index"))
})

//AJAX
let IGESDF =
{

	find:
	{
		url: "",
		data:
		{
			status: "",
			tipo: "",
			b: "",
			ano: "",
			pagina: 1,
		}
	},
	request_get: function (url, data, complete) {

		$.get(url, data)
			.done(function (data) {
				complete(data, true);
			})
			.fail(function () {
				complete(data, false);
			})

	},

	do_find_request: function () {
		document.getElementById("ajax_content").innerHTML += document.getElementById("loading").innerHTML
		this.request_get(this.find.url, this.find.data, function (data, success) {
			if (!success) {
				alert("Erro na requisição");
				document.getElementById("ajax_content").innerHTML = "";
				return;
			}

			document.getElementById("ajax_content").innerHTML = data;
		});
	},
	find_ato_mode() {
		IGESDF.set_find_data("status", "");
		$("[data-setstatus]").removeClass("ativo");
		IGESDF.set_find_data("ano", "");
		$(".slick-current").removeClass("slick-current");

	},
	find_no_ato() {
		$("#search_ato").val("");
		IGESDF.set_find_data("b", "");
	},
	set_find_data(name, value) {

		if (value != "") {
			if (name == "b") {
				this.find_ato_mode();
			}
			else {
				this.find_no_ato();
			}
		}
		if(name != "pagina") this.find.data.pagina = 1;

		this.find.data[name] = value;

	},
	setup_find() {
		let ajax_div = $("#ajax_content");
		let select = $("#selecaofornecedores")
		this.find.url = ajax_div.attr("data-url");
		
		this.set_find_data("tipo", select.val());
		this.do_find_request();
	}

}

window.onload = function () {
	
if($("#ajax_content").length > 0)
{
	IGESDF.setup_find();
}
$("#selecaofornecedores").change(function()
{
	IGESDF.set_find_data("tipo", $(this).val());
	IGESDF.do_find_request();
});

	$("[data-setstatus]").click(function () {
		$("[data-setstatus]").removeClass("ativo");
		let st = $(this).attr("data-setstatus")
		if (st != IGESDF.find.data.status) {
			IGESDF.set_find_data("status", st);
			$(this).addClass("ativo");
		} else
			IGESDF.set_find_data("status", "");


		IGESDF.do_find_request();

	});
	let search_time = null;
	$(document).on("input", "#search_ato", function () {
		if (search_time !== null)
			clearTimeout(search_time);
		IGESDF.set_find_data("b", this.value)
		search_time = setTimeout(function () {

			IGESDF.do_find_request();
		}, 500);

	});

	$(document).on("click", "[data-page]", function (e) {
		e.preventDefault();
		IGESDF.set_find_data("pagina", $(this).attr("data-page"));
		IGESDF.do_find_request();
	});

	$('.center').on('afterChange', function (event, slick, currentSlide) {
		let ano = $('[data-slick-index=' + currentSlide + ']').find("li").text();
		if(ano == IGESDF.find.data.ano){
			ano = "";
			$(".slick-current").removeClass("slick-current");
		}
		IGESDF.set_find_data("ano", ano);
		IGESDF.do_find_request();
	});

}

$('.select-linked').change(function()
{
	let value = $(this).val();
	if(value != "")
		window.location.href = value;
})