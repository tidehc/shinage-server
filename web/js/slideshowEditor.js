// @TODO Transform this Javascript monster into a jQuery plugin to better bind it and better instantiate it
// @TODO Language Strings?

window.SlideshowEditor = {
    container: null,
    selectedSlide: null,
    slides: [],
    saveUrl: "",
    init: function (container, saveUrl) {
        this.saveUrl = saveUrl;
        this.container = $(container);

        // sortable slide list
        var that = this;
        $("#slides", this.container).sortable({
            "items": "> .slide",
            "update": function () {
                that.saveSlides();
            }
        });

        // select slide handler
        $(this.container).on("click", "#slides > .slide", null, $.proxy(this.selectSlide, this));
        $(this.container).on("click", "#slides > .slide .removeSlide", null, $.proxy(this.removeSlideButton, this));

        $(".settings input", this.container).on("change", $.proxy(this.settingsChanged, this));

        $("#btnSaveSettings").on('click', $.proxy(this.saveSlides, this));
        $("#tabAdd .add").on('click', $.proxy(this.addSlideButton, this));

        this.setupAjax();

        return this;
    },
    setupAjax: function() {
        $.ajaxSetup({
            'beforeSend': function () {
                $(document).trigger('notify-hide');
            },
            'success': function () {
                $('.notifyjs-wrapper').trigger('notify-hide');
                $.notify("Gespeichert.", 'success');
            },
            'error': function () {
                $('.notifyjs-wrapper').trigger('notify-hide');
                $.notify("Speichern fehlgeschlagen.", 'error');
            }
        });
    },
    addSlideButton: function (e) {
        //var slide = $(e.currentTarget).data('prototype');
        //this.appendSlide(slide);
        //this.saveSlides();

        $("#choseImageOverlay")
            .modal('setting', 'transition', 'fade up')
            .modal('setting', 'observeChanges', true)
            .modal('setting', 'onVisible', function() {
                $("#selectFilesPane").trigger("resize");
            })
            .modal('setting', 'onApprove', $.proxy(function () {
                $("#selectFilesPane").elfinder('instance').exec('getfile');
                for (var i = 0; i < window.selectedFiles.length; i++) {
                    var file = window.selectedFiles[i];
                    var slide = $(e.currentTarget).data('prototype');
                    slide.src = file.url;
                    this.appendSlide(slide);
                    this.saveSlides();
                }
            }, this))
            .modal('show');

        //initElFinder();
    },
    selectSlide: function (e) {
        var slide = $(e.currentTarget).data("slide");

        // visualization
        $('#slides > .slide', this.container).removeClass('selected');
        $(e.currentTarget).addClass('selected');

        // Write properties in slide settings pane
        for (var property in slide) {
            if (slide.hasOwnProperty(property)) {
                $("#tabSlide .settings input[name="+property+"]", this.container).val(slide[property]);
            }
        }

        // this is the SlideshowEditor object; e.currentTarget the event handler's element
        $("#tabSlide .settings", this.container).hide();
        $("#tabSlide .settings."+slide.type, this.container).show();

        this.selectedSlide = slide;
        $('.tabular.menu .item').tab('change tab', 'tabSlide');
    },
    removeSlideButton: function (e) {
        var slide = $(e.currentTarget).parent();
        slide.remove();
        this.saveSlides();
    },
    settingsChanged: function (e) {
        if (this.selectedSlide === null) {
            return;
        }
        var key = $(e.currentTarget).attr('name');
        var value = $(e.currentTarget).val();
        this.selectedSlide[key] = value;
        this.saveSlides();
    },
    appendSlide: function (slide) {
        var slideDiv = $("#prototypes .prototype.slide."+slide.type, this.container).clone();
        slideDiv.removeClass("prototype");
        slideDiv.attr('data-slide', JSON.stringify(slide));
        this.provisionSlide(slideDiv, slide);

        // add remove button
        slideDiv.append("<div class='removeSlide'><i class='remove icon'></i></div>");

        $("#slides", this.container).append(slideDiv);
        return this;
    },
    loadSlides: function (slides) {
        for (var i = 0; i < slides.length; i++) {
            this.appendSlide(slides[i]);
        }
        return this;
    },
    provisionSlide: function (div, slide) {
        if (slide.type === "Image") {
            this.provisionImageSlide(div, slide);
        }
        if (slide.type === "Video") {
            this.provisionVideoSlide(div, slide);
        }
        return this;
    },
    provisionImageSlide: function (div, slide) {
        $("img", div).attr("src", slide.src);
        return this;
    },
    provisionVideoSlide: function (div, slide) {
        var filename = slide.src.replace(/^.*[\\\/]/, '');
        $(".videoFileName", div).text(filename);
        return this;
    },
    serialize: function() {
        var data = [];
        $("#slides .slide", this.container).each(function () {
            data.push($(this).data("slide"));
        });
        return data;
    },
    saveSlides: function () {
        var data = this.serialize();
        $.post({
            "url": this.saveUrl,
            "data": {
                "slides": JSON.stringify(data)
            },
            "dataType": "json",
            'beforeSend': function () {
                $.notify("Speichern...", {
                    'autoHide': false,
                    'className': 'info',
                    'clickToHide': false
                });
            }
        }).done(function () {
            // do nothing by now
        }).fail(function (e) {
            console.log("Error saving slides: ");
            console.log(e);
        });
    }
};


