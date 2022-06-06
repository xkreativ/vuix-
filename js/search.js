$('.search-input').keyup(() => {
    $('.search-input').jcOnPageFilter();
}) 

jQuery.fn.jcOnPageFilter = function(settings) {
    settings = jQuery.extend({
        focusOnLoad: false,
        highlightColor: false,
        textColorForHighlights: false,
        caseSensitive: false,
        hideNegatives: true,
        parentSectionClass: 'friends__list',
        parentLookupClass: 'friend__item',
        childBlockClass: 'friend__username',
        noFoundClass: 'no-found'
    }, settings);
    jQuery.expr[':'].icontains = function(obj, index, meta) {                    
        return jQuery(obj).text().toUpperCase().indexOf(meta[3].toUpperCase()) >= 0;                
    }; 
    if(settings.focusOnLoad) {
        jQuery(this).focus();
    }
    jQuery('.'+settings.noFoundClass).css("display", "none");
    var rex = /(<span.+?>)(.+?)(<\/span>)/g;
    var rexAtt = "g";
    if(!settings.caseSensitive) {
        rex = /(<span.+?>)(.+?)(<\/span>)/gi;
        rexAtt = "gi";
    }
    return this.each(function() {
        jQuery(this).keyup(function(e) {
            jQuery('.'+settings.parentSectionClass).show();
            jQuery('.'+settings.noFoundClass).hide();                    
            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                return false;
                } else {
                var textToFilter = jQuery(this).val();
                if (textToFilter.length > 0) {
                    if(settings.hideNegatives) {
                        jQuery('.'+settings.parentLookupClass).stop(true, true).hide();
                    }
                    var _cs = "icontains";
                    if(settings.caseSensitive) {
                        _cs = "contains";
                    }
                    jQuery.each(jQuery('.'+settings.childBlockClass),function(i,obj) {
                        jQuery(obj).html(jQuery(obj).html().replace(new RegExp(rex), "$2"));  
                    });
                    jQuery.each(jQuery('.'+settings.childBlockClass+":"+_cs+"(" + textToFilter + ")"),function(i,obj) {
                        if(settings.hideNegatives) {
                            jQuery(obj).closest('.'+settings.parentLookupClass).stop(true, true).show();
                        }
                        var newhtml = jQuery(obj).text();
                        jQuery(obj).html(newhtml.replace(
                            new RegExp(textToFilter, rexAtt), 
                            function(match) {
                                return [ match].join("");
                            }
                        ));
                    });
                    
                    } else {
                    jQuery.each(jQuery('.'+settings.childBlockClass),function(i,obj) {
                        var html = jQuery(obj).html().replace(new RegExp(rex), "$2");
                        jQuery(obj).html(html);  
                    });
                    if(settings.hideNegatives) {
                        jQuery('.'+settings.parentLookupClass).stop(true, true).show();
                    }
                }
            }            
            if (!jQuery('.'+settings.parentLookupClass+':visible').length) {
                jQuery('.'+settings.parentSectionClass).hide();
                jQuery('.'+settings.noFoundClass).show();     
            }    
        });
    });
}; 