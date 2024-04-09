<!doctype html>
<html lang="en-US">
<head>
	<!--<script async type="text/javascript"data-cookieconsent="ignore" src="/build/mainMenu.js"></script>-->
	<script>

function handlMenuButtonClick(e) {
	const mediaQuery = window.matchMedia("(min-width: 782px)");
  	const mainNav = document.querySelector(".mainNav");
	const mainMenu = document.querySelector(".main-menu");
	const secondMenu = document.querySelector(".secondary-menu");
	const secItems = document.querySelectorAll(".sec-item");
	const overlay = document.querySelector(".overlay");
	const menuButton = document.querySelector(".menu-button");
	const fa = document.getElementById("fa-bars");
	const backButton = document.querySelector(".back-icon");

    	fa.classList.toggle("fa-bars")
        fa.classList.toggle("fa-xmark")
        mainMenu.classList.toggle("d-block")
		secondMenu.classList.remove("d-none")
        const megaMenus = document.querySelectorAll(".mega-menu")
        secondMenu.classList.toggle("d-block")
        overlay.classList.toggle("show-overlay")
        backButton.classList.remove("d-block")
        megaMenus.forEach(function (megaMenu) {
            megaMenu.classList.remove("d-block", "level-overlay")
        })
    }

	</script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<script data-cookieconsent="ignore">
	window.dataLayer = window.dataLayer || [];
	function gtag() {
		dataLayer.push(arguments);
	}
	gtag("consent", "default", {
		ad_user_data: "denied",
		ad_personalization: "denied",
		ad_storage: "denied",
		analytics_storage: "denied",
		functionality_storage: "denied",
		personalization_storage: "denied",
		security_storage: "granted",
		wait_for_update: 500,
	});
	gtag("set", "ads_data_redaction", true);
	gtag("set", "url_passthrough", true);
</script>
<script data-cookieconsent="ignore">
		(function (w, d, s, l, i) {
		w[l] = w[l] || []; w[l].push({'gtm.start':new Date().getTime(), event: 'gtm.js'});
		var f = d.getElementsByTagName(s)[0],  j = d.createElement(s), dl = l !== 'dataLayer' ? '&l=' + l : '';
		j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
		f.parentNode.insertBefore(j, f);})(
		window,
		document,
		'script',
		'dataLayer',
		'GTM-MD6KD4S'
	);
</script>
<script type="text/javascript"
		id="Cookiebot"
		src="https://consent.cookiebot.com/uc.js"
		data-cbid="9de8edae-1edd-4351-b320-7b3f6d54d3ce"
							data-blockingmode="auto"
	></script>
<script data-cfasync="false" data-no-defer="1" data-no-minify="1" data-no-optimize="1">var ewww_webp_supported=!1;function check_webp_feature(A,e){var w;e=void 0!==e?e:function(){},ewww_webp_supported?e(ewww_webp_supported):((w=new Image).onload=function(){ewww_webp_supported=0<w.width&&0<w.height,e&&e(ewww_webp_supported)},w.onerror=function(){e&&e(!1)},w.src="data:image/webp;base64,"+{alpha:"UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAABQBAJ0BKgEAAQAAAP4AAA3AAP7mtQAAAA=="}[A])}check_webp_feature("alpha");</script><script data-cfasync="false" data-no-defer="1" data-no-minify="1" data-no-optimize="1">var Arrive=function(c,w){"use strict";if(c.MutationObserver&&"undefined"!=typeof HTMLElement){var r,a=0,u=(r=HTMLElement.prototype.matches||HTMLElement.prototype.webkitMatchesSelector||HTMLElement.prototype.mozMatchesSelector||HTMLElement.prototype.msMatchesSelector,{matchesSelector:function(e,t){return e instanceof HTMLElement&&r.call(e,t)},addMethod:function(e,t,r){var a=e[t];e[t]=function(){return r.length==arguments.length?r.apply(this,arguments):"function"==typeof a?a.apply(this,arguments):void 0}},callCallbacks:function(e,t){t&&t.options.onceOnly&&1==t.firedElems.length&&(e=[e[0]]);for(var r,a=0;r=e[a];a++)r&&r.callback&&r.callback.call(r.elem,r.elem);t&&t.options.onceOnly&&1==t.firedElems.length&&t.me.unbindEventWithSelectorAndCallback.call(t.target,t.selector,t.callback)},checkChildNodesRecursively:function(e,t,r,a){for(var i,n=0;i=e[n];n++)r(i,t,a)&&a.push({callback:t.callback,elem:i}),0<i.childNodes.length&&u.checkChildNodesRecursively(i.childNodes,t,r,a)},mergeArrays:function(e,t){var r,a={};for(r in e)e.hasOwnProperty(r)&&(a[r]=e[r]);for(r in t)t.hasOwnProperty(r)&&(a[r]=t[r]);return a},toElementsArray:function(e){return e=void 0!==e&&("number"!=typeof e.length||e===c)?[e]:e}}),e=(l.prototype.addEvent=function(e,t,r,a){a={target:e,selector:t,options:r,callback:a,firedElems:[]};return this._beforeAdding&&this._beforeAdding(a),this._eventsBucket.push(a),a},l.prototype.removeEvent=function(e){for(var t,r=this._eventsBucket.length-1;t=this._eventsBucket[r];r--)e(t)&&(this._beforeRemoving&&this._beforeRemoving(t),(t=this._eventsBucket.splice(r,1))&&t.length&&(t[0].callback=null))},l.prototype.beforeAdding=function(e){this._beforeAdding=e},l.prototype.beforeRemoving=function(e){this._beforeRemoving=e},l),t=function(i,n){var o=new e,l=this,s={fireOnAttributesModification:!1};return o.beforeAdding(function(t){var e=t.target;e!==c.document&&e!==c||(e=document.getElementsByTagName("html")[0]);var r=new MutationObserver(function(e){n.call(this,e,t)}),a=i(t.options);r.observe(e,a),t.observer=r,t.me=l}),o.beforeRemoving(function(e){e.observer.disconnect()}),this.bindEvent=function(e,t,r){t=u.mergeArrays(s,t);for(var a=u.toElementsArray(this),i=0;i<a.length;i++)o.addEvent(a[i],e,t,r)},this.unbindEvent=function(){var r=u.toElementsArray(this);o.removeEvent(function(e){for(var t=0;t<r.length;t++)if(this===w||e.target===r[t])return!0;return!1})},this.unbindEventWithSelectorOrCallback=function(r){var a=u.toElementsArray(this),i=r,e="function"==typeof r?function(e){for(var t=0;t<a.length;t++)if((this===w||e.target===a[t])&&e.callback===i)return!0;return!1}:function(e){for(var t=0;t<a.length;t++)if((this===w||e.target===a[t])&&e.selector===r)return!0;return!1};o.removeEvent(e)},this.unbindEventWithSelectorAndCallback=function(r,a){var i=u.toElementsArray(this);o.removeEvent(function(e){for(var t=0;t<i.length;t++)if((this===w||e.target===i[t])&&e.selector===r&&e.callback===a)return!0;return!1})},this},i=new function(){var s={fireOnAttributesModification:!1,onceOnly:!1,existing:!1};function n(e,t,r){return!(!u.matchesSelector(e,t.selector)||(e._id===w&&(e._id=a++),-1!=t.firedElems.indexOf(e._id)))&&(t.firedElems.push(e._id),!0)}var c=(i=new t(function(e){var t={attributes:!1,childList:!0,subtree:!0};return e.fireOnAttributesModification&&(t.attributes=!0),t},function(e,i){e.forEach(function(e){var t=e.addedNodes,r=e.target,a=[];null!==t&&0<t.length?u.checkChildNodesRecursively(t,i,n,a):"attributes"===e.type&&n(r,i)&&a.push({callback:i.callback,elem:r}),u.callCallbacks(a,i)})})).bindEvent;return i.bindEvent=function(e,t,r){t=void 0===r?(r=t,s):u.mergeArrays(s,t);var a=u.toElementsArray(this);if(t.existing){for(var i=[],n=0;n<a.length;n++)for(var o=a[n].querySelectorAll(e),l=0;l<o.length;l++)i.push({callback:r,elem:o[l]});if(t.onceOnly&&i.length)return r.call(i[0].elem,i[0].elem);setTimeout(u.callCallbacks,1,i)}c.call(this,e,t,r)},i},o=new function(){var a={};function i(e,t){return u.matchesSelector(e,t.selector)}var n=(o=new t(function(){return{childList:!0,subtree:!0}},function(e,r){e.forEach(function(e){var t=e.removedNodes,e=[];null!==t&&0<t.length&&u.checkChildNodesRecursively(t,r,i,e),u.callCallbacks(e,r)})})).bindEvent;return o.bindEvent=function(e,t,r){t=void 0===r?(r=t,a):u.mergeArrays(a,t),n.call(this,e,t,r)},o};d(HTMLElement.prototype),d(NodeList.prototype),d(HTMLCollection.prototype),d(HTMLDocument.prototype),d(Window.prototype);var n={};return s(i,n,"unbindAllArrive"),s(o,n,"unbindAllLeave"),n}function l(){this._eventsBucket=[],this._beforeAdding=null,this._beforeRemoving=null}function s(e,t,r){u.addMethod(t,r,e.unbindEvent),u.addMethod(t,r,e.unbindEventWithSelectorOrCallback),u.addMethod(t,r,e.unbindEventWithSelectorAndCallback)}function d(e){e.arrive=i.bindEvent,s(i,e,"unbindArrive"),e.leave=o.bindEvent,s(o,e,"unbindLeave")}}(window,void 0),ewww_webp_supported=!1;function check_webp_feature(e,t){var r;ewww_webp_supported?t(ewww_webp_supported):((r=new Image).onload=function(){ewww_webp_supported=0<r.width&&0<r.height,t(ewww_webp_supported)},r.onerror=function(){t(!1)},r.src="data:image/webp;base64,"+{alpha:"UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAABQBAJ0BKgEAAQAAAP4AAA3AAP7mtQAAAA==",animation:"UklGRlIAAABXRUJQVlA4WAoAAAASAAAAAAAAAAAAQU5JTQYAAAD/////AABBTk1GJgAAAAAAAAAAAAAAAAAAAGQAAABWUDhMDQAAAC8AAAAQBxAREYiI/gcA"}[e])}function ewwwLoadImages(e){if(e){for(var t=document.querySelectorAll(".batch-image img, .image-wrapper a, .ngg-pro-masonry-item a, .ngg-galleria-offscreen-seo-wrapper a"),r=0,a=t.length;r<a;r++)ewwwAttr(t[r],"data-src",t[r].getAttribute("data-webp")),ewwwAttr(t[r],"data-thumbnail",t[r].getAttribute("data-webp-thumbnail"));for(var i=document.querySelectorAll(".rev_slider ul li"),r=0,a=i.length;r<a;r++){ewwwAttr(i[r],"data-thumb",i[r].getAttribute("data-webp-thumb"));for(var n=1;n<11;)ewwwAttr(i[r],"data-param"+n,i[r].getAttribute("data-webp-param"+n)),n++}for(r=0,a=(i=document.querySelectorAll(".rev_slider img")).length;r<a;r++)ewwwAttr(i[r],"data-lazyload",i[r].getAttribute("data-webp-lazyload"));for(var o=document.querySelectorAll("div.woocommerce-product-gallery__image"),r=0,a=o.length;r<a;r++)ewwwAttr(o[r],"data-thumb",o[r].getAttribute("data-webp-thumb"))}for(var l=document.querySelectorAll("video"),r=0,a=l.length;r<a;r++)ewwwAttr(l[r],"poster",e?l[r].getAttribute("data-poster-webp"):l[r].getAttribute("data-poster-image"));for(var s,c=document.querySelectorAll("img.ewww_webp_lazy_load"),r=0,a=c.length;r<a;r++)e&&(ewwwAttr(c[r],"data-lazy-srcset",c[r].getAttribute("data-lazy-srcset-webp")),ewwwAttr(c[r],"data-srcset",c[r].getAttribute("data-srcset-webp")),ewwwAttr(c[r],"data-lazy-src",c[r].getAttribute("data-lazy-src-webp")),ewwwAttr(c[r],"data-src",c[r].getAttribute("data-src-webp")),ewwwAttr(c[r],"data-orig-file",c[r].getAttribute("data-webp-orig-file")),ewwwAttr(c[r],"data-medium-file",c[r].getAttribute("data-webp-medium-file")),ewwwAttr(c[r],"data-large-file",c[r].getAttribute("data-webp-large-file")),null!=(s=c[r].getAttribute("srcset"))&&!1!==s&&s.includes("R0lGOD")&&ewwwAttr(c[r],"src",c[r].getAttribute("data-lazy-src-webp"))),c[r].className=c[r].className.replace(/\bewww_webp_lazy_load\b/,"");for(var w=document.querySelectorAll(".ewww_webp"),r=0,a=w.length;r<a;r++)e?(ewwwAttr(w[r],"srcset",w[r].getAttribute("data-srcset-webp")),ewwwAttr(w[r],"src",w[r].getAttribute("data-src-webp")),ewwwAttr(w[r],"data-orig-file",w[r].getAttribute("data-webp-orig-file")),ewwwAttr(w[r],"data-medium-file",w[r].getAttribute("data-webp-medium-file")),ewwwAttr(w[r],"data-large-file",w[r].getAttribute("data-webp-large-file")),ewwwAttr(w[r],"data-large_image",w[r].getAttribute("data-webp-large_image")),ewwwAttr(w[r],"data-src",w[r].getAttribute("data-webp-src"))):(ewwwAttr(w[r],"srcset",w[r].getAttribute("data-srcset-img")),ewwwAttr(w[r],"src",w[r].getAttribute("data-src-img"))),w[r].className=w[r].className.replace(/\bewww_webp\b/,"ewww_webp_loaded");window.jQuery&&jQuery.fn.isotope&&jQuery.fn.imagesLoaded&&(jQuery(".fusion-posts-container-infinite").imagesLoaded(function(){jQuery(".fusion-posts-container-infinite").hasClass("isotope")&&jQuery(".fusion-posts-container-infinite").isotope()}),jQuery(".fusion-portfolio:not(.fusion-recent-works) .fusion-portfolio-wrapper").imagesLoaded(function(){jQuery(".fusion-portfolio:not(.fusion-recent-works) .fusion-portfolio-wrapper").isotope()}))}function ewwwWebPInit(e){ewwwLoadImages(e),ewwwNggLoadGalleries(e),document.arrive(".ewww_webp",function(){ewwwLoadImages(e)}),document.arrive(".ewww_webp_lazy_load",function(){ewwwLoadImages(e)}),document.arrive("videos",function(){ewwwLoadImages(e)}),"loading"==document.readyState?document.addEventListener("DOMContentLoaded",ewwwJSONParserInit):("undefined"!=typeof galleries&&ewwwNggParseGalleries(e),ewwwWooParseVariations(e))}function ewwwAttr(e,t,r){null!=r&&!1!==r&&e.setAttribute(t,r)}function ewwwJSONParserInit(){"undefined"!=typeof galleries&&check_webp_feature("alpha",ewwwNggParseGalleries),check_webp_feature("alpha",ewwwWooParseVariations)}function ewwwWooParseVariations(e){if(e)for(var t=document.querySelectorAll("form.variations_form"),r=0,a=t.length;r<a;r++){var i=t[r].getAttribute("data-product_variations"),n=!1;try{for(var o in i=JSON.parse(i))void 0!==i[o]&&void 0!==i[o].image&&(void 0!==i[o].image.src_webp&&(i[o].image.src=i[o].image.src_webp,n=!0),void 0!==i[o].image.srcset_webp&&(i[o].image.srcset=i[o].image.srcset_webp,n=!0),void 0!==i[o].image.full_src_webp&&(i[o].image.full_src=i[o].image.full_src_webp,n=!0),void 0!==i[o].image.gallery_thumbnail_src_webp&&(i[o].image.gallery_thumbnail_src=i[o].image.gallery_thumbnail_src_webp,n=!0),void 0!==i[o].image.thumb_src_webp&&(i[o].image.thumb_src=i[o].image.thumb_src_webp,n=!0));n&&ewwwAttr(t[r],"data-product_variations",JSON.stringify(i))}catch(e){}}}function ewwwNggParseGalleries(e){if(e)for(var t in galleries){var r=galleries[t];galleries[t].images_list=ewwwNggParseImageList(r.images_list)}}function ewwwNggLoadGalleries(e){e&&document.addEventListener("ngg.galleria.themeadded",function(e,t){window.ngg_galleria._create_backup=window.ngg_galleria.create,window.ngg_galleria.create=function(e,t){var r=$(e).data("id");return galleries["gallery_"+r].images_list=ewwwNggParseImageList(galleries["gallery_"+r].images_list),window.ngg_galleria._create_backup(e,t)}})}function ewwwNggParseImageList(e){for(var t in e){var r=e[t];if(void 0!==r["image-webp"]&&(e[t].image=r["image-webp"],delete e[t]["image-webp"]),void 0!==r["thumb-webp"]&&(e[t].thumb=r["thumb-webp"],delete e[t]["thumb-webp"]),void 0!==r.full_image_webp&&(e[t].full_image=r.full_image_webp,delete e[t].full_image_webp),void 0!==r.srcsets)for(var a in r.srcsets)nggSrcset=r.srcsets[a],void 0!==r.srcsets[a+"-webp"]&&(e[t].srcsets[a]=r.srcsets[a+"-webp"],delete e[t].srcsets[a+"-webp"]);if(void 0!==r.full_srcsets)for(var i in r.full_srcsets)nggFSrcset=r.full_srcsets[i],void 0!==r.full_srcsets[i+"-webp"]&&(e[t].full_srcsets[i]=r.full_srcsets[i+"-webp"],delete e[t].full_srcsets[i+"-webp"])}return e}check_webp_feature("alpha",ewwwWebPInit);</script><meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />

	<!-- This site is optimized with the Yoast SEO Premium plugin v22.3 (Yoast SEO v22.3) - https://yoast.com/wordpress/plugins/seo/ -->
	<title>Blog | StackPath</title>
	<meta name="description" content="A behind-the-stack view of our product releases, recommended practices, and company updates." />
	<link rel="canonical" href="https://www.stackpath.com/blog/" />
	<link rel="next" href="https://www.stackpath.com/blog/page/2/" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Blog Archives" />
	<meta property="og:description" content="A behind-the-stack view of our product releases, recommended practices, and company updates." />
	<meta property="og:url" content="https://www.stackpath.com/blog/" />
	<meta property="og:site_name" content="StackPath" />
	<!-- REPLACED BY PLACID: <-m-e-t-a-replaced name="twitter:card" content="summary_large_image" /> -->
	<meta name="twitter:site" content="@stackpath" />
	<script type="application/ld+json" class="yoast-schema-graph">{"@context":"https://schema.org","@graph":[{"@type":"CollectionPage","@id":"https://www.stackpath.com/blog/","url":"https://www.stackpath.com/blog/","name":"Blog | StackPath","isPartOf":{"@id":"https://www.stackpath.com/#website"},"description":"A behind-the-stack view of our product releases, recommended practices, and company updates.","breadcrumb":{"@id":"https://www.stackpath.com/blog/#breadcrumb"},"inLanguage":"en-US"},{"@type":"BreadcrumbList","@id":"https://www.stackpath.com/blog/#breadcrumb","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://www.stackpath.com/"},{"@type":"ListItem","position":2,"name":"Blog"}]},{"@type":"WebSite","@id":"https://www.stackpath.com/#website","url":"https://www.stackpath.com/","name":"StackPath","description":"Build Your Edge","publisher":{"@id":"https://www.stackpath.com/#organization"},"potentialAction":[{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"https://www.stackpath.com/?s={search_term_string}"},"query-input":"required name=search_term_string"}],"inLanguage":"en-US"},{"@type":"Organization","@id":"https://www.stackpath.com/#organization","name":"StackPath","url":"https://www.stackpath.com/","logo":{"@type":"ImageObject","inLanguage":"en-US","@id":"https://www.stackpath.com/#/schema/logo/image/","url":"https://www.stackpath.com/wp-content/uploads/2022/06/brand-asset-preview-logo-reverse@2x.png","contentUrl":"https://www.stackpath.com/wp-content/uploads/2022/06/brand-asset-preview-logo-reverse@2x.png","width":800,"height":800,"caption":"StackPath"},"image":{"@id":"https://www.stackpath.com/#/schema/logo/image/"},"sameAs":["https://facebook.com/stackpathllc","https://twitter.com/stackpath","https://www.linkedin.com/company/stackpath/"]}]}</script>
	<!-- / Yoast SEO Premium plugin. -->


<link rel='dns-prefetch' href='//kit.fontawesome.com' />
<link rel='dns-prefetch' href='//js.hs-scripts.com' />
<link rel="alternate" type="application/rss+xml" title="StackPath &raquo; Feed" href="https://www.stackpath.com/feed/" />
<link rel="alternate" type="application/rss+xml" title="StackPath &raquo; Comments Feed" href="https://www.stackpath.com/comments/feed/" />
<link rel="alternate" type="application/rss+xml" title="StackPath &raquo; Blog Category Feed" href="https://www.stackpath.com/blog/feed/" />
<style id='esab-accordion-style-inline-css'>
.wp-block-esab-accordion{position:relative}.wp-block-esab-accordion .esab__container{display:flex;flex-direction:column;justify-content:space-between}.wp-block-esab-accordion .wp-block-esab-accordion-child{box-sizing:border-box;overflow:hidden}.wp-block-esab-accordion .wp-block-esab-accordion-child.bs__one{box-shadow:0 8px 24px hsla(210,8%,62%,.2)}.wp-block-esab-accordion .wp-block-esab-accordion-child.bs__two{box-shadow:0 7px 29px 0 hsla(240,5%,41%,.2)}.wp-block-esab-accordion .wp-block-esab-accordion-child.bs__three{box-shadow:0 5px 15px rgba(0,0,0,.35)}.wp-block-esab-accordion .esab__head{align-items:center;display:flex;justify-content:space-between}.wp-block-esab-accordion .esab__head.esab__head_reverse{flex-direction:row-reverse}.wp-block-esab-accordion .esab__head.esab__head_reverse .esab__heading_txt{margin-left:.5em;margin-right:0}.wp-block-esab-accordion .esab__head .esab__heading_txt{margin-left:0;margin-right:.5em;width:100%}.wp-block-esab-accordion .esab__heading_tag{margin:0!important;padding:0!important}.wp-block-esab-accordion .esab__head{cursor:pointer}.wp-block-esab-accordion .esab__icon{cursor:pointer;height:30px;line-height:40px;position:relative;text-align:center;width:30px}.wp-block-esab-accordion .esab__icon .esab__collapse,.wp-block-esab-accordion .esab__icon .esab__expand{height:100%;left:0;position:absolute;top:0;width:100%}.wp-block-esab-accordion .esab__icon .esab__expand,.wp-block-esab-accordion .esab__icon.esab__active_icon .esab__collapse{display:none}.wp-block-esab-accordion .esab__icon.esab__active_icon .esab__expand{display:block}.wp-block-esab-accordion .esab__body{display:none}

</style>
<style id='global-styles-inline-css'>
body{--wp--preset--color--black: #000000;--wp--preset--color--cyan-bluish-gray: #abb8c3;--wp--preset--color--white: #ffffff;--wp--preset--color--pale-pink: #f78da7;--wp--preset--color--vivid-red: #cf2e2e;--wp--preset--color--luminous-vivid-orange: #ff6900;--wp--preset--color--luminous-vivid-amber: #fcb900;--wp--preset--color--light-green-cyan: #7bdcb5;--wp--preset--color--vivid-green-cyan: #00d084;--wp--preset--color--pale-cyan-blue: #8ed1fc;--wp--preset--color--vivid-cyan-blue: #0693e3;--wp--preset--color--vivid-purple: #9b51e0;--wp--preset--color--sp-flash-white: #ffffff;--wp--preset--color--sp-meteor-black: #000000;--wp--preset--color--sp-cobalt-blue-bright-2: #2b2bff;--wp--preset--color--sp-cobalt-blue-base: #0c00b2;--wp--preset--color--sp-cobalt-blue-dark-1: #0a0087;--wp--preset--color--sp-electric-blue-bright-2: #0095de;--wp--preset--color--sp-electric-blue-base: #026699;--wp--preset--color--sp-electric-blue-dark: #004e74;--wp--preset--color--sp-metallic-teal-bright-2: #009ead;--wp--preset--color--sp-metallic-teal-base: #005e66;--wp--preset--color--sp-metallic-teal-dark-1: #00474e;--wp--preset--color--sp-circuit-green-bright-2: #05bd6e;--wp--preset--color--sp-circuit-green-base: #00804b;--wp--preset--color--sp-circuit-green-dark-1: #006139;--wp--preset--color--sp-chrome-yellow-bright-2: #ffd426;--wp--preset--color--sp-chrome-yellow-base: #f0b400;--wp--preset--color--sp-chrome-yellow-dark-1: #b88300;--wp--preset--color--sp-inferno-orange-bright-2: #fd6411;--wp--preset--color--sp-inferno-orange-base: #cc4700;--wp--preset--color--sp-inferno-orange-dark-1: #a33600;--wp--preset--color--sp-resistor-red-bright-2: #e80215;--wp--preset--color--sp-resistor-red-base: #99010d;--wp--preset--color--sp-resistor-red-dark-1: #74000a;--wp--preset--color--sp-fusion-pink-bright-2: #e300da;--wp--preset--color--sp-fusion-pink-base: #990092;--wp--preset--color--sp-fusion-pink-dark-1: #74006f;--wp--preset--color--sp-cyborg-purple-bright-2: #aa00f9;--wp--preset--color--sp-cyborg-purple-base: #680099;--wp--preset--color--sp-cyborg-purple-dark-1: #4f0074;--wp--preset--color--sp-battleship-gray-bright-5: #efeff1;--wp--preset--color--sp-battleship-gray-bright-2: #b6b6bc;--wp--preset--color--sp-battleship-gray-dark-5: #1c1c1f;--wp--preset--color--sp-trans-black: #04040633;--wp--preset--color--sp-trans-white: #ffffff4d;--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%);--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%);--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%);--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%);--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%);--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%);--wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%);--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%);--wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%);--wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%);--wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%);--wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%);--wp--preset--font-size--small: 13px;--wp--preset--font-size--medium: 20px;--wp--preset--font-size--large: 36px;--wp--preset--font-size--x-large: 42px;--wp--preset--font-size--display-2-xl: 14rem;--wp--preset--font-size--display-xl: 10.5rem;--wp--preset--font-size--display-l: 7.875rem;;--wp--preset--font-size--display-m: 5.938rem;--wp--preset--font-size--display-s: 4.438rem;--wp--preset--font-size--display-xs: 3.313rem;--wp--preset--font-size--display-2-xs: 2.5rem;--wp--preset--font-size--headline-2-xl: 5.625rem;--wp--preset--font-size--headline-xl: 4.25rem;--wp--preset--font-size--headline-l: 3.25rem;--wp--preset--font-size--headline-m: 2.375rem;--wp--preset--font-size--headline-s: 1.75rem;--wp--preset--font-size--headline-xs: 1.313rem;--wp--preset--font-size--headline-2-xs: 1rem;--wp--preset--font-size--body-2-xl: 1.438rem;--wp--preset--font-size--body-2-xl-light: 23.008px;--wp--preset--font-size--body-xl: 1.25rem;--wp--preset--font-size--body-l: 1.125rem;--wp--preset--font-size--body-m: 16px;--wp--preset--font-size--body-s: 0.875rem;--wp--preset--font-size--body-xs: 0.75rem;--wp--preset--font-size--body-2-xs: 0.688rem;--wp--preset--font-family--rama-gothic: rama-gothic-m, Arial, sans-serif;--wp--preset--font-family--roboto: 'Roboto', sans-serif;;--wp--preset--font-family--roboto-mono: 'Roboto Mono', monospace;;--wp--preset--spacing--20: 0.44rem;--wp--preset--spacing--30: 0.67rem;--wp--preset--spacing--40: 1rem;--wp--preset--spacing--50: 1.5rem;--wp--preset--spacing--60: 2.25rem;--wp--preset--spacing--70: 3.38rem;--wp--preset--spacing--80: 5.06rem;--wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);--wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);--wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);--wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);--wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);}body { margin: 0;--wp--style--global--content-size: 1024px;--wp--style--global--wide-size: 1088px; }.wp-site-blocks > .alignleft { float: left; margin-right: 2em; }.wp-site-blocks > .alignright { float: right; margin-left: 2em; }.wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }:where(.wp-site-blocks) > * { margin-block-start: 2rem; margin-block-end: 0; }:where(.wp-site-blocks) > :first-child:first-child { margin-block-start: 0; }:where(.wp-site-blocks) > :last-child:last-child { margin-block-end: 0; }body { --wp--style--block-gap: 2rem; }:where(body .is-layout-flow)  > :first-child:first-child{margin-block-start: 0;}:where(body .is-layout-flow)  > :last-child:last-child{margin-block-end: 0;}:where(body .is-layout-flow)  > *{margin-block-start: 2rem;margin-block-end: 0;}:where(body .is-layout-constrained)  > :first-child:first-child{margin-block-start: 0;}:where(body .is-layout-constrained)  > :last-child:last-child{margin-block-end: 0;}:where(body .is-layout-constrained)  > *{margin-block-start: 2rem;margin-block-end: 0;}:where(body .is-layout-flex) {gap: 2rem;}:where(body .is-layout-grid) {gap: 2rem;}body .is-layout-flow > .alignleft{float: left;margin-inline-start: 0;margin-inline-end: 2em;}body .is-layout-flow > .alignright{float: right;margin-inline-start: 2em;margin-inline-end: 0;}body .is-layout-flow > .aligncenter{margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > .alignleft{float: left;margin-inline-start: 0;margin-inline-end: 2em;}body .is-layout-constrained > .alignright{float: right;margin-inline-start: 2em;margin-inline-end: 0;}body .is-layout-constrained > .aligncenter{margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)){max-width: var(--wp--style--global--content-size);margin-left: auto !important;margin-right: auto !important;}body .is-layout-constrained > .alignwide{max-width: var(--wp--style--global--wide-size);}body .is-layout-flex{display: flex;}body .is-layout-flex{flex-wrap: wrap;align-items: center;}body .is-layout-flex > *{margin: 0;}body .is-layout-grid{display: grid;}body .is-layout-grid > *{margin: 0;}body{padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px;}a:where(:not(.wp-element-button)){text-decoration: underline;}.wp-element-button, .wp-block-button__link{background-color: transparent;border-radius: 0;border-width: 0;color: #ffffff;font-family: inherit;font-size: 1rem;font-weight: 700;line-height: inherit;padding-top: 8px;padding-right: 12px;padding-bottom: 8px;padding-left: 12px;text-decoration: none;}.has-black-color{color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-color{color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-color{color: var(--wp--preset--color--white) !important;}.has-pale-pink-color{color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-color{color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-color{color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-color{color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-color{color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-color{color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-color{color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-color{color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-color{color: var(--wp--preset--color--vivid-purple) !important;}.has-sp-flash-white-color{color: var(--wp--preset--color--sp-flash-white) !important;}.has-sp-meteor-black-color{color: var(--wp--preset--color--sp-meteor-black) !important;}.has-sp-cobalt-blue-bright-2-color{color: var(--wp--preset--color--sp-cobalt-blue-bright-2) !important;}.has-sp-cobalt-blue-base-color{color: var(--wp--preset--color--sp-cobalt-blue-base) !important;}.has-sp-cobalt-blue-dark-1-color{color: var(--wp--preset--color--sp-cobalt-blue-dark-1) !important;}.has-sp-electric-blue-bright-2-color{color: var(--wp--preset--color--sp-electric-blue-bright-2) !important;}.has-sp-electric-blue-base-color{color: var(--wp--preset--color--sp-electric-blue-base) !important;}.has-sp-electric-blue-dark-color{color: var(--wp--preset--color--sp-electric-blue-dark) !important;}.has-sp-metallic-teal-bright-2-color{color: var(--wp--preset--color--sp-metallic-teal-bright-2) !important;}.has-sp-metallic-teal-base-color{color: var(--wp--preset--color--sp-metallic-teal-base) !important;}.has-sp-metallic-teal-dark-1-color{color: var(--wp--preset--color--sp-metallic-teal-dark-1) !important;}.has-sp-circuit-green-bright-2-color{color: var(--wp--preset--color--sp-circuit-green-bright-2) !important;}.has-sp-circuit-green-base-color{color: var(--wp--preset--color--sp-circuit-green-base) !important;}.has-sp-circuit-green-dark-1-color{color: var(--wp--preset--color--sp-circuit-green-dark-1) !important;}.has-sp-chrome-yellow-bright-2-color{color: var(--wp--preset--color--sp-chrome-yellow-bright-2) !important;}.has-sp-chrome-yellow-base-color{color: var(--wp--preset--color--sp-chrome-yellow-base) !important;}.has-sp-chrome-yellow-dark-1-color{color: var(--wp--preset--color--sp-chrome-yellow-dark-1) !important;}.has-sp-inferno-orange-bright-2-color{color: var(--wp--preset--color--sp-inferno-orange-bright-2) !important;}.has-sp-inferno-orange-base-color{color: var(--wp--preset--color--sp-inferno-orange-base) !important;}.has-sp-inferno-orange-dark-1-color{color: var(--wp--preset--color--sp-inferno-orange-dark-1) !important;}.has-sp-resistor-red-bright-2-color{color: var(--wp--preset--color--sp-resistor-red-bright-2) !important;}.has-sp-resistor-red-base-color{color: var(--wp--preset--color--sp-resistor-red-base) !important;}.has-sp-resistor-red-dark-1-color{color: var(--wp--preset--color--sp-resistor-red-dark-1) !important;}.has-sp-fusion-pink-bright-2-color{color: var(--wp--preset--color--sp-fusion-pink-bright-2) !important;}.has-sp-fusion-pink-base-color{color: var(--wp--preset--color--sp-fusion-pink-base) !important;}.has-sp-fusion-pink-dark-1-color{color: var(--wp--preset--color--sp-fusion-pink-dark-1) !important;}.has-sp-cyborg-purple-bright-2-color{color: var(--wp--preset--color--sp-cyborg-purple-bright-2) !important;}.has-sp-cyborg-purple-base-color{color: var(--wp--preset--color--sp-cyborg-purple-base) !important;}.has-sp-cyborg-purple-dark-1-color{color: var(--wp--preset--color--sp-cyborg-purple-dark-1) !important;}.has-sp-battleship-gray-bright-5-color{color: var(--wp--preset--color--sp-battleship-gray-bright-5) !important;}.has-sp-battleship-gray-bright-2-color{color: var(--wp--preset--color--sp-battleship-gray-bright-2) !important;}.has-sp-battleship-gray-dark-5-color{color: var(--wp--preset--color--sp-battleship-gray-dark-5) !important;}.has-sp-trans-black-color{color: var(--wp--preset--color--sp-trans-black) !important;}.has-sp-trans-white-color{color: var(--wp--preset--color--sp-trans-white) !important;}.has-black-background-color{background-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-background-color{background-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-background-color{background-color: var(--wp--preset--color--white) !important;}.has-pale-pink-background-color{background-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-background-color{background-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-background-color{background-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-background-color{background-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-background-color{background-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-background-color{background-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-background-color{background-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-background-color{background-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-background-color{background-color: var(--wp--preset--color--vivid-purple) !important;}.has-sp-flash-white-background-color{background-color: var(--wp--preset--color--sp-flash-white) !important;}.has-sp-meteor-black-background-color{background-color: var(--wp--preset--color--sp-meteor-black) !important;}.has-sp-cobalt-blue-bright-2-background-color{background-color: var(--wp--preset--color--sp-cobalt-blue-bright-2) !important;}.has-sp-cobalt-blue-base-background-color{background-color: var(--wp--preset--color--sp-cobalt-blue-base) !important;}.has-sp-cobalt-blue-dark-1-background-color{background-color: var(--wp--preset--color--sp-cobalt-blue-dark-1) !important;}.has-sp-electric-blue-bright-2-background-color{background-color: var(--wp--preset--color--sp-electric-blue-bright-2) !important;}.has-sp-electric-blue-base-background-color{background-color: var(--wp--preset--color--sp-electric-blue-base) !important;}.has-sp-electric-blue-dark-background-color{background-color: var(--wp--preset--color--sp-electric-blue-dark) !important;}.has-sp-metallic-teal-bright-2-background-color{background-color: var(--wp--preset--color--sp-metallic-teal-bright-2) !important;}.has-sp-metallic-teal-base-background-color{background-color: var(--wp--preset--color--sp-metallic-teal-base) !important;}.has-sp-metallic-teal-dark-1-background-color{background-color: var(--wp--preset--color--sp-metallic-teal-dark-1) !important;}.has-sp-circuit-green-bright-2-background-color{background-color: var(--wp--preset--color--sp-circuit-green-bright-2) !important;}.has-sp-circuit-green-base-background-color{background-color: var(--wp--preset--color--sp-circuit-green-base) !important;}.has-sp-circuit-green-dark-1-background-color{background-color: var(--wp--preset--color--sp-circuit-green-dark-1) !important;}.has-sp-chrome-yellow-bright-2-background-color{background-color: var(--wp--preset--color--sp-chrome-yellow-bright-2) !important;}.has-sp-chrome-yellow-base-background-color{background-color: var(--wp--preset--color--sp-chrome-yellow-base) !important;}.has-sp-chrome-yellow-dark-1-background-color{background-color: var(--wp--preset--color--sp-chrome-yellow-dark-1) !important;}.has-sp-inferno-orange-bright-2-background-color{background-color: var(--wp--preset--color--sp-inferno-orange-bright-2) !important;}.has-sp-inferno-orange-base-background-color{background-color: var(--wp--preset--color--sp-inferno-orange-base) !important;}.has-sp-inferno-orange-dark-1-background-color{background-color: var(--wp--preset--color--sp-inferno-orange-dark-1) !important;}.has-sp-resistor-red-bright-2-background-color{background-color: var(--wp--preset--color--sp-resistor-red-bright-2) !important;}.has-sp-resistor-red-base-background-color{background-color: var(--wp--preset--color--sp-resistor-red-base) !important;}.has-sp-resistor-red-dark-1-background-color{background-color: var(--wp--preset--color--sp-resistor-red-dark-1) !important;}.has-sp-fusion-pink-bright-2-background-color{background-color: var(--wp--preset--color--sp-fusion-pink-bright-2) !important;}.has-sp-fusion-pink-base-background-color{background-color: var(--wp--preset--color--sp-fusion-pink-base) !important;}.has-sp-fusion-pink-dark-1-background-color{background-color: var(--wp--preset--color--sp-fusion-pink-dark-1) !important;}.has-sp-cyborg-purple-bright-2-background-color{background-color: var(--wp--preset--color--sp-cyborg-purple-bright-2) !important;}.has-sp-cyborg-purple-base-background-color{background-color: var(--wp--preset--color--sp-cyborg-purple-base) !important;}.has-sp-cyborg-purple-dark-1-background-color{background-color: var(--wp--preset--color--sp-cyborg-purple-dark-1) !important;}.has-sp-battleship-gray-bright-5-background-color{background-color: var(--wp--preset--color--sp-battleship-gray-bright-5) !important;}.has-sp-battleship-gray-bright-2-background-color{background-color: var(--wp--preset--color--sp-battleship-gray-bright-2) !important;}.has-sp-battleship-gray-dark-5-background-color{background-color: var(--wp--preset--color--sp-battleship-gray-dark-5) !important;}.has-sp-trans-black-background-color{background-color: var(--wp--preset--color--sp-trans-black) !important;}.has-sp-trans-white-background-color{background-color: var(--wp--preset--color--sp-trans-white) !important;}.has-black-border-color{border-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-border-color{border-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-border-color{border-color: var(--wp--preset--color--white) !important;}.has-pale-pink-border-color{border-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-border-color{border-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-border-color{border-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-border-color{border-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-border-color{border-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-border-color{border-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-border-color{border-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-border-color{border-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-border-color{border-color: var(--wp--preset--color--vivid-purple) !important;}.has-sp-flash-white-border-color{border-color: var(--wp--preset--color--sp-flash-white) !important;}.has-sp-meteor-black-border-color{border-color: var(--wp--preset--color--sp-meteor-black) !important;}.has-sp-cobalt-blue-bright-2-border-color{border-color: var(--wp--preset--color--sp-cobalt-blue-bright-2) !important;}.has-sp-cobalt-blue-base-border-color{border-color: var(--wp--preset--color--sp-cobalt-blue-base) !important;}.has-sp-cobalt-blue-dark-1-border-color{border-color: var(--wp--preset--color--sp-cobalt-blue-dark-1) !important;}.has-sp-electric-blue-bright-2-border-color{border-color: var(--wp--preset--color--sp-electric-blue-bright-2) !important;}.has-sp-electric-blue-base-border-color{border-color: var(--wp--preset--color--sp-electric-blue-base) !important;}.has-sp-electric-blue-dark-border-color{border-color: var(--wp--preset--color--sp-electric-blue-dark) !important;}.has-sp-metallic-teal-bright-2-border-color{border-color: var(--wp--preset--color--sp-metallic-teal-bright-2) !important;}.has-sp-metallic-teal-base-border-color{border-color: var(--wp--preset--color--sp-metallic-teal-base) !important;}.has-sp-metallic-teal-dark-1-border-color{border-color: var(--wp--preset--color--sp-metallic-teal-dark-1) !important;}.has-sp-circuit-green-bright-2-border-color{border-color: var(--wp--preset--color--sp-circuit-green-bright-2) !important;}.has-sp-circuit-green-base-border-color{border-color: var(--wp--preset--color--sp-circuit-green-base) !important;}.has-sp-circuit-green-dark-1-border-color{border-color: var(--wp--preset--color--sp-circuit-green-dark-1) !important;}.has-sp-chrome-yellow-bright-2-border-color{border-color: var(--wp--preset--color--sp-chrome-yellow-bright-2) !important;}.has-sp-chrome-yellow-base-border-color{border-color: var(--wp--preset--color--sp-chrome-yellow-base) !important;}.has-sp-chrome-yellow-dark-1-border-color{border-color: var(--wp--preset--color--sp-chrome-yellow-dark-1) !important;}.has-sp-inferno-orange-bright-2-border-color{border-color: var(--wp--preset--color--sp-inferno-orange-bright-2) !important;}.has-sp-inferno-orange-base-border-color{border-color: var(--wp--preset--color--sp-inferno-orange-base) !important;}.has-sp-inferno-orange-dark-1-border-color{border-color: var(--wp--preset--color--sp-inferno-orange-dark-1) !important;}.has-sp-resistor-red-bright-2-border-color{border-color: var(--wp--preset--color--sp-resistor-red-bright-2) !important;}.has-sp-resistor-red-base-border-color{border-color: var(--wp--preset--color--sp-resistor-red-base) !important;}.has-sp-resistor-red-dark-1-border-color{border-color: var(--wp--preset--color--sp-resistor-red-dark-1) !important;}.has-sp-fusion-pink-bright-2-border-color{border-color: var(--wp--preset--color--sp-fusion-pink-bright-2) !important;}.has-sp-fusion-pink-base-border-color{border-color: var(--wp--preset--color--sp-fusion-pink-base) !important;}.has-sp-fusion-pink-dark-1-border-color{border-color: var(--wp--preset--color--sp-fusion-pink-dark-1) !important;}.has-sp-cyborg-purple-bright-2-border-color{border-color: var(--wp--preset--color--sp-cyborg-purple-bright-2) !important;}.has-sp-cyborg-purple-base-border-color{border-color: var(--wp--preset--color--sp-cyborg-purple-base) !important;}.has-sp-cyborg-purple-dark-1-border-color{border-color: var(--wp--preset--color--sp-cyborg-purple-dark-1) !important;}.has-sp-battleship-gray-bright-5-border-color{border-color: var(--wp--preset--color--sp-battleship-gray-bright-5) !important;}.has-sp-battleship-gray-bright-2-border-color{border-color: var(--wp--preset--color--sp-battleship-gray-bright-2) !important;}.has-sp-battleship-gray-dark-5-border-color{border-color: var(--wp--preset--color--sp-battleship-gray-dark-5) !important;}.has-sp-trans-black-border-color{border-color: var(--wp--preset--color--sp-trans-black) !important;}.has-sp-trans-white-border-color{border-color: var(--wp--preset--color--sp-trans-white) !important;}.has-vivid-cyan-blue-to-vivid-purple-gradient-background{background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;}.has-light-green-cyan-to-vivid-green-cyan-gradient-background{background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;}.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;}.has-luminous-vivid-orange-to-vivid-red-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;}.has-very-light-gray-to-cyan-bluish-gray-gradient-background{background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;}.has-cool-to-warm-spectrum-gradient-background{background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;}.has-blush-light-purple-gradient-background{background: var(--wp--preset--gradient--blush-light-purple) !important;}.has-blush-bordeaux-gradient-background{background: var(--wp--preset--gradient--blush-bordeaux) !important;}.has-luminous-dusk-gradient-background{background: var(--wp--preset--gradient--luminous-dusk) !important;}.has-pale-ocean-gradient-background{background: var(--wp--preset--gradient--pale-ocean) !important;}.has-electric-grass-gradient-background{background: var(--wp--preset--gradient--electric-grass) !important;}.has-midnight-gradient-background{background: var(--wp--preset--gradient--midnight) !important;}.has-small-font-size{font-size: var(--wp--preset--font-size--small) !important;}.has-medium-font-size{font-size: var(--wp--preset--font-size--medium) !important;}.has-large-font-size{font-size: var(--wp--preset--font-size--large) !important;}.has-x-large-font-size{font-size: var(--wp--preset--font-size--x-large) !important;}.has-display-2-xl-font-size{font-size: var(--wp--preset--font-size--display-2-xl) !important;}.has-display-xl-font-size{font-size: var(--wp--preset--font-size--display-xl) !important;}.has-display-l-font-size{font-size: var(--wp--preset--font-size--display-l) !important;}.has-display-m-font-size{font-size: var(--wp--preset--font-size--display-m) !important;}.has-display-s-font-size{font-size: var(--wp--preset--font-size--display-s) !important;}.has-display-xs-font-size{font-size: var(--wp--preset--font-size--display-xs) !important;}.has-display-2-xs-font-size{font-size: var(--wp--preset--font-size--display-2-xs) !important;}.has-headline-2-xl-font-size{font-size: var(--wp--preset--font-size--headline-2-xl) !important;}.has-headline-xl-font-size{font-size: var(--wp--preset--font-size--headline-xl) !important;}.has-headline-l-font-size{font-size: var(--wp--preset--font-size--headline-l) !important;}.has-headline-m-font-size{font-size: var(--wp--preset--font-size--headline-m) !important;}.has-headline-s-font-size{font-size: var(--wp--preset--font-size--headline-s) !important;}.has-headline-xs-font-size{font-size: var(--wp--preset--font-size--headline-xs) !important;}.has-headline-2-xs-font-size{font-size: var(--wp--preset--font-size--headline-2-xs) !important;}.has-body-2-xl-font-size{font-size: var(--wp--preset--font-size--body-2-xl) !important;}.has-body-2-xl-light-font-size{font-size: var(--wp--preset--font-size--body-2-xl-light) !important;}.has-body-xl-font-size{font-size: var(--wp--preset--font-size--body-xl) !important;}.has-body-l-font-size{font-size: var(--wp--preset--font-size--body-l) !important;}.has-body-m-font-size{font-size: var(--wp--preset--font-size--body-m) !important;}.has-body-s-font-size{font-size: var(--wp--preset--font-size--body-s) !important;}.has-body-xs-font-size{font-size: var(--wp--preset--font-size--body-xs) !important;}.has-body-2-xs-font-size{font-size: var(--wp--preset--font-size--body-2-xs) !important;}.has-rama-gothic-font-family{font-family: var(--wp--preset--font-family--rama-gothic) !important;}.has-roboto-font-family{font-family: var(--wp--preset--font-family--roboto) !important;}.has-roboto-mono-font-family{font-family: var(--wp--preset--font-family--roboto-mono) !important;}
.wp-block-navigation a:where(:not(.wp-element-button)){color: inherit;}
.wp-block-pullquote{font-size: 1.5em;line-height: 1.6;}
</style>
<link rel='stylesheet' id='heateor_sss_frontend_css-css' href='https://www.stackpath.com/wp-content/plugins/sassy-social-share/public/css/sassy-social-share-public.css?ver=3.3.60' media='all' />
<style id='heateor_sss_frontend_css-inline-css'>
.heateor_sss_button_instagram span.heateor_sss_svg,a.heateor_sss_instagram span.heateor_sss_svg{background:radial-gradient(circle at 30% 107%,#fdf497 0,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285aeb 90%)}.heateor_sss_horizontal_sharing .heateor_sss_svg,.heateor_sss_standard_follow_icons_container .heateor_sss_svg{color:#fff;border-width:0px;border-style:solid;border-color:transparent}.heateor_sss_horizontal_sharing .heateorSssTCBackground{color:#666}.heateor_sss_horizontal_sharing span.heateor_sss_svg:hover,.heateor_sss_standard_follow_icons_container span.heateor_sss_svg:hover{border-color:transparent;}.heateor_sss_vertical_sharing span.heateor_sss_svg,.heateor_sss_floating_follow_icons_container span.heateor_sss_svg{color:#fff;border-width:0px;border-style:solid;border-color:transparent;}.heateor_sss_vertical_sharing .heateorSssTCBackground{color:#666;}.heateor_sss_vertical_sharing span.heateor_sss_svg:hover,.heateor_sss_floating_follow_icons_container span.heateor_sss_svg:hover{border-color:transparent;}@media screen and (max-width:783px) {.heateor_sss_vertical_sharing{display:none!important}}
</style>
<link rel='stylesheet' id='parent-critical-css' href='https://www.stackpath.com/wp-content/themes/StackPath/build/critical.css?ver=1.0.0' media='all' />
<script src="https://www.stackpath.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
<script src="https://www.stackpath.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
<script id="wpstg-global-js-extra">
var wpstg = {"nonce":"ae2c17d37d"};
</script>
<script src="https://www.stackpath.com/wp-content/plugins/wp-staging-pro/assets/js/dist/wpstg-blank-loader.js?ver=6.5" id="wpstg-global-js"></script>
<script src="https://www.stackpath.com/wp-content/themes/StackPath/build/mainMenu.js?ver=1.0.0&#039; async=&#039;async" id="main-menu-js"></script>
<meta name="generator" content="WordPress 6.5" />
<!-- HFCM by 99 Robots - Snippet # 1: GTM Head -->
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MD6KD4S');</script>
<!-- End Google Tag Manager -->
<!-- /end HFCM by 99 Robots -->
			<!-- DO NOT COPY THIS SNIPPET! Start of Page Analytics Tracking for HubSpot WordPress plugin v11.0.28-->
			<script class="hsq-set-content-id" data-content-id="listing-page">
				var _hsq = _hsq || [];
				_hsq.push(["setContentType", "listing-page"]);
			</script>
			<!-- DO NOT COPY THIS SNIPPET! End of Page Analytics Tracking for HubSpot WordPress plugin -->
			<noscript><style>.lazyload[data-src]{display:none !important;}</style></noscript><style>.lazyload{background-image:none !important;}.lazyload:before{background-image:none !important;}</style><link rel="icon" href="https://www.stackpath.com/wp-content/uploads/2023/07/cropped-android-chrome-512x512-1-32x32.png" sizes="32x32" />
<link rel="icon" href="https://www.stackpath.com/wp-content/uploads/2023/07/cropped-android-chrome-512x512-1-192x192.png" sizes="192x192" />
<link rel="apple-touch-icon" href="https://www.stackpath.com/wp-content/uploads/2023/07/cropped-android-chrome-512x512-1-180x180.png" />
<meta name="msapplication-TileImage" content="https://www.stackpath.com/wp-content/uploads/2023/07/cropped-android-chrome-512x512-1-270x270.png" />
		<style id="wp-custom-css">
			@media screen and (min-width: 601px) {
	#CybotCookiebotDialogBodyButtonsWrapper {
		flex-direction: row !important;
	}
}

#CybotCookiebotDialogHeader {
	display: none !important;
}

#CybotCookiebotDialogTabContent {
	margin-bottom: 10px !important;
	margin-left: 0 !important;
	max-width: 1200px !important;
}

.CybotCookiebotDialogBodyBottomWrapper {
	display: none !important;
}

#CybotCookiebotDialogBodyButtons {
	max-width: none !important;
}

.CybotCookiebotDialogContentWrapper {
	flex-direction: column !important;
	max-width: 1024px !important;
}

@media screen and (min-width: 601px) {
	.CybotCookiebotDialogBodyButton {
	margin-bottom: 0 !important;
}
}

#CybotCookiebotDialogPoweredByText {
	display: none !important;
}

.CybotCookiebotDialogNavItem:last-child {
	display: none !important
}

/*Cookie Page */
.post-8971 .CookieDeclarationDialogText {
	display: none
}

#CookieDeclarationConsentIdAndDate {
    width: auto !important;
    word-wrap: break-word;
    overflow: hidden;
}		</style>
		<!-- PLACID META TAGS -->
    <!-- Facebook OG Image -->
    <meta property="og:image" content="https://www.stackpath.com/wp-content/uploads/placid-social-images/bea436fbf6253b4aa7810cfd7dd9b199.png"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:image:width" content="1200"/>
    <!-- Twitter Card Image -->
    <meta name="twitter:image" content="https://www.stackpath.com/wp-content/uploads/placid-social-images/bea436fbf6253b4aa7810cfd7dd9b199.png"/>
    <meta name="twitter:card" content="summary_large_image">
<!-- /PLACID META TAGS -->

<script type="text/javascript">window.WPSI_is_processing = false;</script>


</head>

<body class="archive category category-blog category-1 wp-custom-logo">
<script data-cfasync="false" data-no-defer="1" data-no-minify="1" data-no-optimize="1">if(typeof ewww_webp_supported==="undefined"){var ewww_webp_supported=!1}if(ewww_webp_supported){document.body.classList.add("webp-support")}</script>
<a class="skip-link screen-reader-text d-none" href="#primary">Skip to content</a>
<div id="megaWrapper" class="mega-wrapper">
	<header id="masthead" class="mainNav">
		<div class="the-logo">
			<a href="https://www.stackpath.com/" class="img-fluid site-logo -link" rel="home"><img width="512" height="71" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAABHAQAAAABZwcKqAAAAAnRSTlMAAHaTzTgAAAAaSURBVFjD7cEBAQAAAIIg/69uSEABAAAAcGASBwAB+r7pQgAAAABJRU5ErkJggg==" class="img-fluid site-logo  lazyload ewww_webp_lazy_load" alt="StackPath" decoding="async"   data-src="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png" data-srcset="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png 512w, https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w-300x42.png 300w" data-sizes="auto" data-eio-rwidth="512" data-eio-rheight="71" data-src-webp="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png.webp" data-srcset-webp="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png.webp 512w, https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w-300x42.png.webp 300w" /><noscript><img width="512" height="71" src="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png" class="img-fluid site-logo " alt="StackPath" decoding="async" srcset="https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w.png 512w, https://www.stackpath.com/wp-content/uploads/2023/11/cropped-stackpath-logo-black-rgb-512w-300x42.png 300w" sizes="(max-width: 512px) 100vw, 512px" data-eio="l" /></noscript></a>		</div>

		<div class="logo-mobile">
			<a href="https://www.stackpath.com/" rel="home">
				<img class="site-logo lazyload" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG8AAAArAQAAAABed+u3AAAAAnRSTlMAAHaTzTgAAAAPSURBVCjPY2AYBaNggAEAAoUAAfDzWbUAAAAASUVORK5CYII=" alt="StackPath" data-src="https://www.stackpath.com/wp-content/themes/StackPath/img/mobile-logo-no-padding.png" decoding="async" data-eio-rwidth="111" data-eio-rheight="43"><noscript><img class="site-logo" src="https://www.stackpath.com/wp-content/themes/StackPath/img/mobile-logo-no-padding.png" alt="StackPath" data-eio="l"></noscript>
			</a>
		</div>

		<div class="main-menu">
			<ul> 
				<li class="sec-item" id="sec-item" >
					<span class="d-flex justify-content-between" id="parentOne">
						Platform
						<i class="fa-sharp fa-regular fa-angle-right"></i>
					</span>

					<div class="mega-menu" id="childOne">
						<div class="content py-0 bg-white">
							<div class="mega-col">
								<section>
									<p class="pt-0 sub-menu-title">Platform</p>
									<ul class="row mega-links mega-menu-wrapper">
									<li><a href="https://www.stackpath.com/platform/">Why Stackpath</a>
										<div class="description">Cloud. But closer.</div>
									</li>
									<li><a href="https://www.stackpath.com/platform/edgeengine/">EdgeEngine™</a>
										<div class="description">Unified cloud orchestration.</div>
									</li>
									<li><a href="https://www.stackpath.com/platform/edge-locations/">Network & Locations</a>
										<div class="description">High proximity and distribution.</div>
									</li>
									</ul>
								</section>
							</div>

						</div>

						<div class="mega-sub">
							<div class="content py-0">
								<section>
									<ul class="row mega-menu-wrapper">
										<li class="col-12"><a href="/contact-sales">Contact Sales</a>
											<div class="description">Contact us for a demo and pricing.</div>
										</li>
										<li class="col-12"><a href="/lp/24brnd057-03-new-customer-credit/">$500 Credit for New Customers</a>
											<div class="description">Learn about this limited time offer.</div>
										</li>
										<li>
										</li>
									</ul>
								</section>
							</div>
						</div>

					</div>
				</li>
				<li class="sec-item" id="sec-item" >
					<span class="d-flex justify-content-between" id="parentTwo">
						Products
						<i class="fa-sharp fa-regular fa-angle-right"></i>
					</span>

					<div class="mega-menu" id="childTwo">
						<div class="content py-0 bg-white">
							<div class="mega-col">
								<section>
									<p class="pt-0 sub-menu-title">Products</p>
									<ul class="mega-links mega-menu-wrapper row">
										<li><a href="https://www.stackpath.com/products/containers/">SP// Containers</a>
											<div class="description">Configure, control, and run container images.</div>
										</li>
										<li><a href="https://www.stackpath.com/products/virtual-machines/">SP// Virtual Machines</a>
											<div class="description">Complete computing environments, including OS.</div>
										</li>
										<!-- <li><a href="/resources/support/">SP// Support</a>
											<div class="description">Lorem ipsum dolor sit amet, consectetur</div>
										</li> -->
									</ul>
									</section>
									<!-- <section>
										<hr />
										<p class="pt-0">Features</p>
										<ul class="mega-links mega-menu-wrapper row">
											<li><a href="">Menu Item</a>
												<div class="description">Nullam quis risus eget urna mollis</div>
											</li>
										</ul>
								</section>								 -->
							</div>
						</div>
						<div class="mega-sub">
							<div class="content py-0">
								<section>
									<ul class="row mega-menu-wrapper">
										<li><a href="https://www.stackpath.com/products/edge-compute/">Learn About Edge Compute</a>
											<div class="description">Understand its infrastructure, uses, and advantages.</div>
										</li>
										<li class="col-12"><a href="/lp/24brnd057-03-new-customer-credit/">$500 Credit for New Customers</a>
											<div class="description">Learn about this limited time offer.</div>
										</li>
									</ul>
								</section>
							</div>
						</div>
					</div>
				</li>

				<!-- <li class="sec-item" id="sec-item" >
					<span class="d-flex justify-content-between" id="parentTwo">
						Solutions
						<i class="fa-sharp fa-regular fa-angle-right"></i>
					</span>
					<div class="mega-menu" id="childTwo">
						<div class="content py-0 bg-white">
							<div class="mega-col">
								<section>
									<p class="pt-0">Market Solutions</p>
									<ul class="mega-links mega-menu-wrapper row">
										<li><a href="">Menu Item</a>
											<div class="description">Add a third promotional item if it's needed.</div>
										</li>
									</ul>
								</section>
							</div>
						</div>
					</div>
				</li> -->


				<li class="sec-item" id="sec-item" >
					<span class="d-flex justify-content-between" id="parentFour">
						Resources
						<i class="fa-sharp fa-regular fa-angle-right"></i>
					</span>
					<div class="mega-menu" id="childFour">
						<div class="content py-0 bg-white">
							<div class="mega-col">
								<section>
								<p class="pt-0 sub-menu-title">Resources</p>
									<ul class="mega-links mega-menu-wrapper row">
										<li><a href="http://support.stackpath.com">Knowledgebase</a>
											<div class="description">SP// product guides and release notes.</div>
										</li>
										<li><a href="https://www.stackpath.com/resources/developers">Developer Resources</a>
											<div class="description">API docs, network and service status updates.</div>
										</li>
										<li><a href="https://www.stackpath.com/edge-academy">Edge Academy™</a>
											<div class="description">Edge computing tech and concepts explained.</div>
										</li>
										<li><a href="https://www.stackpath.com/resources/sales-partner-programs">Sales Partner Program</a>
											<div class="description">Our platform, your edge. Partner with us.</div>
										</li>
										<li><a href="https://www.stackpath.com/blog">Blog</a>
											<div class="description">Get a view from the edge.</div>
										</li>
										<li class="p-0">
											<div class="description"></div>
										</li>
									</ul>
								</section>								
							</div>
						</div>
						<div class="mega-sub">
							<div class="content py-0">
								<section>
									<ul class="row mega-menu-wrapper">
										<li><a href="https://www.stackpath.com/press">Press Releases</a>
											<div class="description">SP// news and announcements.</div>
										</li>
										<li class="col-12"><a href="/lp/24brnd057-03-new-customer-credit/">$500 Credit for New Customers</a>
											<div class="description">Learn about this limited time offer.</div>
										</li>
										<li>
										</li>
									</ul>
								</section>
							</div>
						</div>
					</div>
				</li>
				
			</ul>
		</div>
		<div class="secondary-menu">
			<ul>
				<li class="search">
					<div class="search-container position-absolute">
						<form action="https://www.stackpath.com/search/" method="get" class="search collapsed d-none d-md-block" id="search">
    <input type="text" id="search" name="q" placeholder="Search..." value="">
    <a onclick="slideSearch()"></a>
    <button type="submit" class="submit-btn">Submit</button>
</form> 
					</div>
				</li>
				<li class="secondary-item login"><a href="https://control.stackpath.com/" target="_blank">Log In</a></li>
				<li class="secondary-item button"><a href="/contact-sales/">Get Demo</a></li>
				<li class="secondary-item button outline"><a href="https://control.stackpath.com/register/?_ga=2.138664054.413738689.1655916795-307854368.1648648321" target="_blank">Get Started</a></li>
				<li class="search-mobile">
					<div class="search-container position-absolute">
						<form action="https://www.stackpath.com/search/" method="get" class="search" id="search">
    <input class="my-search" type="text" name="q" placeholder="Search..." value="">
    <i class="fa-sharp fa-regular fa-magnifying-glass"></i>
    <button type="submit" class="submit-btn">Submit</button>
</form> 
					</div>
				</li>
			</ul>
		</div>
		<div class="back-icon has-roboto-mono-font-family">
			<i class="fa-sharp fa-regular fa-chevron-left"></i>
			Back
		</div>
		<a href="javascript:void(0);" class="icon menu-button" id="icon" onclick="handlMenuButtonClick()">
			<i class="fa-sharp fa-regular fa-bars menu-bars" id="fa-bars"></i>
		</a>
	</header><!-- #masthead --> 
	<div id="Modal" class="overlay"></div>
</div>
<main id="primary" class="site-main blog">
	

<div class="hero hero-resource bg-black text-white">
	<div class="resource-text-container is-layout-constrained wp-block-group">
				<h1 class="resource-title has-rama-gothic-font-family has-display-l-font-size border-stack">SP//<br/>Blog</h1>
				<div class="resource-description border-bottom-light">
			<p>A behind-the-stack view of our product releases, recommended practices, and company updates.</p>
		</div>
					</div>
</div>		<div class="is-layout-constrained wp-block-group">
			<div class="layout-blog">
				<div class="main-column ps-lg-0">
					<div class="card-grid">
																		
																					
	
<article id="post-11797" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/nvidia-gpu-accelerated-computing-at-the-edge-of-the-internet/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">StackPath Introduces NVIDIA GPU Accelerated Edge Compute Instances </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/nvidia-gpu-accelerated-computing-at-the-edge-of-the-internet/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					

We are delighted to announce GPU-Accelerated StackPath Edge Compute Virtual Machine (VM) and Container instances powered by the NVIDIA A2 Tensor Core ...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Oct 25 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-compute/">Edge Compute</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/nvidia-gpu-accelerated-computing-at-the-edge-of-the-internet/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-11797 -->

	
<article id="post-11416" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/introducing-new-larger-sp-edge-compute-instances/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">Introducing New, Larger SP// Edge Compute Instances  </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/introducing-new-larger-sp-edge-compute-instances/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					
We are thrilled to announce significant additions to the StackPath Edge Compute lineup with the introduction of new and larger instances featuring higher virtu...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Aug 15 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-compute/">Edge Compute</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/introducing-new-larger-sp-edge-compute-instances/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-11416 -->

	
<article id="post-10547" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/introducing-virtual-kubelet-support-for-edge-compute-giving-k8s-clusters-an-edge/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">Introducing Virtual Kubelet Support for Edge Compute: Giving K8s Clusters an Edge. </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/introducing-virtual-kubelet-support-for-edge-compute-giving-k8s-clusters-an-edge/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					
We’re excited to announce a new capability that involves three of our favorite things: Kubernetes (K8s), edge computing, and multi-cloud. (Hang on—this isn...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Jun 28 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-compute/">Edge Compute</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/introducing-virtual-kubelet-support-for-edge-compute-giving-k8s-clusters-an-edge/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-10547 -->

	
<article id="post-9099" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/achieving-security-excellence-stackpath-obtains-iso-27001-certification/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">Achieving Security Excellence: StackPath Obtains ISO 27001 Certification  </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/achieving-security-excellence-stackpath-obtains-iso-27001-certification/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					

We’re excited to announce that StackPath has received ISO/IEC 27001 certification. This is a significant achievement for our edge computing platform. Moreov...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				May 11 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-security/">Edge Security</a></span> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/general/">General</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/achieving-security-excellence-stackpath-obtains-iso-27001-certification/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-9099 -->

	
<article id="post-8432" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/api-discovery-now-available-for-sp-waf/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">API Discovery Now Available For SP// WAF </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/api-discovery-now-available-for-sp-waf/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					
Importance of API Security



There is a widespread and rapidly increasing reliance on APIs in applications and architectures today. Unfortunately, this rapid ...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Apr 18 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-security/">Edge Security</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/api-discovery-now-available-for-sp-waf/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-8432 -->

	
<article id="post-8431" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/l3-l4-ddos-protection-is-now-available-on-sp-edge-compute-workloads/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">L3-L4 DDoS Protection is Now Available on SP// Edge Compute Workloads </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/l3-l4-ddos-protection-is-now-available-on-sp-edge-compute-workloads/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					
The increasing digitization of business tasks and assets is driving employers and users to seek more secure software and services. This digitization makes DDoS...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Apr 11 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-compute/">Edge Compute</a></span> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-security/">Edge Security</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/l3-l4-ddos-protection-is-now-available-on-sp-edge-compute-workloads/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-8431 -->

	
<article id="post-8430" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/stackpath-waf-named-leader-in-security-efficacy-2-years-in-a-row/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">StackPath WAF Named Leader in Security Efficacy 2 Years in a Row </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/stackpath-waf-named-leader-in-security-efficacy-2-years-in-a-row/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					

In December of 2021, we proudly reported that our StackPath WAF product was named a “leader” in SecureIQLab’s first Cloud Web Application Firewall Cyber...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Feb 21 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-security/">Edge Security</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/stackpath-waf-named-leader-in-security-efficacy-2-years-in-a-row/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-8430 -->

	
<article id="post-8429" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/ipv6-now-available-on-sp-edge-compute-workloads/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">IPv6 Now Available on SP// Edge Compute Workloads </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/ipv6-now-available-on-sp-edge-compute-workloads/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					

Here at StackPath, we’ve been working hard to provide you with the most cutting-edge and versatile edge compute platform. With the internet exponentially gr...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Jan 26 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-compute/">Edge Compute</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/ipv6-now-available-on-sp-edge-compute-workloads/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-8429 -->

	
<article id="post-5406" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/security-insights-increase-visibility-into-potential-threats/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">Security Insights – Increase Visibility into Potential Threats </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/security-insights-increase-visibility-into-potential-threats/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					
As the threat landscape evolves at a rapid pace, keeping up can be an overwhelming challenge.



StackPath WAF customers can now take advantage of Security Ins...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Jan 19 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/edge-security/">Edge Security</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/security-insights-increase-visibility-into-potential-threats/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-5406 -->

	
<article id="post-5181" class="card-resource card-blog">
	<div class="title">
		<span class="card-title-hat">
			<a class="has-roboto-mono-font-family" href="/blog">Blog</a>
		</span>
		<a href="https://www.stackpath.com/blog/introducing-stackpath-support-plans/" class=" link-unstyled link-unstyled-dark">
			<h2 class="card-title has-headline-s-font-size ">Introducing StackPath Support Plans </h2>
		</a>
	</div>
	
	<div class="card-bottom-container">
					<a href="https://www.stackpath.com/blog/introducing-stackpath-support-plans/" class="link-unstyled link-unstyled-dark">
				<div class="card-excerpt">
					

Everybody needs help, sometimes. And that’s especially true when it comes to adopting new technologies or starting to use a solution from a new provider.


...
				</div><!-- .entry-content -->
			</a>
							<p class="card-date has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-calendar"></i>
				Jan 4 2023			</p>
							<div class="card-tags has-roboto-mono-font-family"><i class="fa-sharp fa-solid fa-tag"></i> 
								<span class="tag"><a class="tag has-roboto-mono-font-family" href="https://www.stackpath.com/topic/general/">General</a></span> 
								<!--<span class="tag">Tag</span> --></div>
				<a href="https://www.stackpath.com/blog/introducing-stackpath-support-plans/" class="card-link"><i
				class="fa-sharp fa-solid fa-arrow-right"></i></a>
</article><!-- #post-5181 -->
											</div>

					
	<nav class="navigation light" aria-label=" ">
		<h2 class="screen-reader-text"> </h2>
		<div class="nav-links"><span aria-current="page" class="page-numbers current">1</span>
<a class="page-numbers" href="https://www.stackpath.com/blog/page/2/">2</a>
<span class="page-numbers dots">&hellip;</span>
<a class="page-numbers" href="https://www.stackpath.com/blog/page/18/">18</a>
<a class="next page-numbers" href="https://www.stackpath.com/blog/page/2/">OLDER</a></div>
	</nav>
					
				</div>

				<div class="blog-sidebar-wrapper">
					
<div class="sidebar additional-resources" id="sidebar-resources">
	
<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle d-flex justify-content-between" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Additional Resources
        <i class="fa-sharp fa-regular fa-angle-down"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="https://www.stackpath.com/blog/">Blog</a>
        <a class="dropdown-item" href="https://www.stackpath.com/edge-academy/">Edge Academy</a>
        <a class="dropdown-item" href="https://www.stackpath.com/case-studies/">Case Studies</a>
        <a class="dropdown-item" href="https://www.stackpath.com/press/">Press</a>
    </div>
</div>
	<div class="row">
		
		<div class="col col-6">
			
<div class="request-demo py-3">
    <p class="fw-bold mb-0">Request a Demo</p>
    <p class="mb-2">Schedule time with our edge experts for a StackPath demo tailored to your needs.</p>
    <a href="https://www.stackpath.com/contact-sales/"><i class="fa-sharp fa-regular fa-arrow-right"></i></a>
</div>		</div>

		<div class="col col-6">
			
<div class="subscribe py-3">
    <p class="fw-bold mb-0">Subscribe</p>
    <p class="mb-2">Stay informed of the latest SP//  updates and solutions.</p>
    <a href="https://www.stackpath.com/email-opt-in/"><i class="fa-sharp fa-regular fa-arrow-right"></i></a>
</div>		</div>

		<div class="col col-6">
			
<div class="follow-sp py-3">
    <p class="fw-bold mb-0">Follow SP//</p>
    <p class="mb-2">Byte-sized activity and announcements:</p>

    <div class="icons d-flex">
        <a href="https://www.facebook.com/StackPathLLC/" target="_blank"><i class="fa-brands fa-facebook"></i></a>
        <a href="https://twitter.com/StackPath/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.linkedin.com/company/stackpath/" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
    </div>
</div>		</div>
	</div>
</div>				</div>
			</div>
		</div>
</main><!-- #main -->


<script>



const footerDrop = (menu) => {
    const parent = document.getElementById( `footer_${menu}` );
    const child = document.getElementById( `footer_child_${menu}`);
    
    parent.classList.toggle('spin');
    child.classList.toggle('collapse');

}


</script>

<footer class="footer-wrapper bg-black">
    <!-- test -->
    <div class="is-layout-constrained wp-block-group">
        <div class="row">
            <div class="footer-menu">
                <h5 class="has-headline-2-xs-font-size" id="footer_1" type="button" data-toggle="collapse" data-target="#collapseSales" aria-expanded="false" aria-controls="collapseExample"  onclick="footerDrop('1')">Sales <i class="fa-sharp fa-solid fa-chevron-down down" aria-hidden="true"></i></h5>
                <div id="footer_child_1" class="collapse dont-collapse-sm" id="collapseSales">
                    <ul class="well">
                        <li><a href="https://www.stackpath.com/contact-sales/">Email </a></li>
                        <li><a href="tel:+18776292361">+1 (877) 629-2361 (US) </a></li>
                        <li><a href="tel:13233131206">+1 (323) 313-1206 (International)</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-menu">
                <h5 class="has-headline-2-xs-font-size" id="footer_2" type="button" data-toggle="collapse" data-target="#collapseWhy" aria-expanded="false" aria-controls="collapseExample" onclick="footerDrop('2')">Platform <i class="fa-sharp fa-solid fa-chevron-down down" aria-hidden="true"></i></h5>
                <div id="footer_child_2" class="collapse dont-collapse-sm" id="collapseWhy">
                    <ul class="well">
                        <li><a href="https://www.stackpath.com/platform/">Our Platform</a></li>
                        <li><a href="https://www.stackpath.com/platform/edgeengine">EdgeEngine™</a></li>
                        <li><a href="https://www.stackpath.com/platform/edge-locations">Network & Locations</a></li>   
                    </ul>
                </div>
                <h5 class="has-headline-2-xs-font-size" id="footer_3" type="button" data-toggle="collapse" data-target="#collapseProducts" aria-expanded="false" aria-controls="collapseExample" onclick="footerDrop('3')">Products<i class="fa-sharp fa-solid fa-chevron-down down" aria-hidden="true"></i></h5>
                <div id="footer_child_3" class="collapse dont-collapse-sm" id="collapseProducts">
                    <ul class="well">
                        <li><a href="https://www.stackpath.com/products/edge-compute/">Edge Compute</a></li>
                        <li><a href="https://www.stackpath.com/products/virtual-machines">Virtual Machines</a></li>
                        <li><a href="https://www.stackpath.com/products/containers">Containers</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-menu">
                <h5 class="has-headline-2-xs-font-size" id="footer_5" class="mt-md-4" type="button" data-toggle="collapse" data-target="#collapseResources" aria-expanded="false" aria-controls="collapseExample" onclick="footerDrop('5')">Resources <i class="fa-sharp fa-solid fa-chevron-down down" aria-hidden="true"></i></h5>
                <div id="footer_child_5" class="collapse dont-collapse-sm" id="collapseResources">
                    <ul class="well">
                        <li><a href="https://www.stackpath.com/resources/support">Support</a></li>
                        <li><a href="https://stackpath.dev/" targe="_blank">API Docs</a></li>
                        <li><a href="https://www.stackpath.com/resources/developers/">Developer Resources</a></li>
                        <li><a href="https://www.stackpath.com/resources/sales-partner-programs/">Sales Partner Programs</a></li>
                        <li><a href="https://www.stackpath.com/edge-academy/">Edge Academy</a></li>
                        <li><a href="https://www.stackpath.com/blog/">Blog</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-menu">
                <h5 class="has-headline-2-xs-font-size" id="footer_6" type="button" data-toggle="collapse" data-target="#collapseCompany" aria-expanded="false" aria-controls="collapseExample" onclick="footerDrop('6')">Company <i class="fa-sharp fa-solid fa-chevron-down down" aria-hidden="true"></i></h5>
                <div id="footer_child_6" class="collapse dont-collapse-sm" id="collapseCompany">
                    <ul class="well">
                        <li><a href="https://www.stackpath.com/company/about-us/">About Us </a></li>
                        <li><a href="https://www.stackpath.com/company/team/">Leadership Team</a></li>
                        <li><a href="https://www.stackpath.com/company/customers/">Customers</a></li>
                        <li> <a href="https://www.stackpath.com/company/careers/">Careers</a></li>
                        <li> <a href="https://www.stackpath.com/company/events/">Events</a></li>
                        <li> <a href="https://www.stackpath.com/news/">News</a></li>
                        <li> <a href="https://www.stackpath.com/press/">Press Releases</a></li>
                        <li> <a href="https://www.stackpath.com/company/logo-and-branding/">Logo & Branding</a></li>
                        <li> <a href="https://www.stackpath.com/compliance/">Compliance</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row bottom-row">
            <div class="col-lg-12 bottom-col align-items-center">
                <ul class="legal-footer-menu">
                    <li class="legal-menu-item"><span class="d-block d-md-flex">© StackPath, LLC</span></li>
                    <li class="legal-menu-item"><span class="d-block d-md-flex">All rights reserved</span></li>
                    <li class="legal-menu-item"><a href="https://www.stackpath.com/legal/">Legal</a></li>
                    <li class="legal-menu-item"><a href="https://www.stackpath.com/legal/privacy-statement/">Privacy Statement</a></li>
                    <li class="legal-menu-item"><a href="https://www.stackpath.com/cookies-information-and-policy/">Cookie Preferences</a></li>
                    <li class="legal-menu-item"><a href="https://www.stackpath.com/legal/california-notice-at-collection-and-privacy-policy">CA Privacy Policy</a></li>
                    <li class="legal-menu-item"><a href="https://www.stackpath.com/cookies-information-and-policy/">Do Not Sell or Share My Information</a></li>
                </ul>
            </div>
        </div>
        <div class="row logo-row mt-0">
            <div class="col-lg-12 logo-col d-flex align-items-center">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAAYAQAAAACOuqP/AAAAAnRSTlMAAHaTzTgAAAAMSURBVAjXY2AYngAAANgAAehTIP8AAAAASUVORK5CYII=" alt="" class="img-fluid footer-logo lazyload ewww_webp_lazy_load" data-src="https://www.stackpath.com/wp-content/themes/StackPath/img/monogram-white.png" decoding="async" data-eio-rwidth="60" data-eio-rheight="24" data-src-webp="https://www.stackpath.com/wp-content/themes/StackPath/img/monogram-white.png.webp"><noscript><img src="https://www.stackpath.com/wp-content/themes/StackPath/img/monogram-white.png" alt="" class="img-fluid footer-logo" data-eio="l"></noscript>

                <ul class="social-menu">
                    <li><a href="https://www.facebook.com/StackPathLLC/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                    <li><a href="https://twitter.com/StackPath/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                    <li><a href="https://www.linkedin.com/company/stackpath/" target="_blank"><i class="fa-brands fa-linkedin"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</div><!-- #page -->

<!-- HFCM by 99 Robots - Snippet # 2: GTM Body -->
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MD6KD4S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- /end HFCM by 99 Robots -->
<script type="text/javascript" src="https://www.stackpath.com/wp-content/plugins/ewww-image-optimizer/includes/lazysizes.min.js?ver=740" data-cookieconsent="ignore"></script><script src="https://kit.fontawesome.com/3a75edc192.js?ver=1.4.3" id="fa6-js"></script>
<script id="leadin-script-loader-js-js-extra">
var leadin_wordpress = {"userRole":"visitor","pageType":"archive","leadinPluginVersion":"11.0.28"};
</script>
<script src="https://js.hs-scripts.com/43963146.js?integration=WordPress&amp;ver=11.0.28" id="leadin-script-loader-js-js"></script>
<script id="heateor_sss_sharing_js-js-before">
function heateorSssLoadEvent(e) {var t=window.onload;if (typeof window.onload!="function") {window.onload=e}else{window.onload=function() {t();e()}}};	var heateorSssSharingAjaxUrl = 'https://www.stackpath.com/wp-admin/admin-ajax.php', heateorSssCloseIconPath = 'https://www.stackpath.com/wp-content/plugins/sassy-social-share/public/../images/close.png', heateorSssPluginIconPath = 'https://www.stackpath.com/wp-content/plugins/sassy-social-share/public/../images/logo.png', heateorSssHorizontalSharingCountEnable = 0, heateorSssVerticalSharingCountEnable = 0, heateorSssSharingOffset = -10; var heateorSssMobileStickySharingEnabled = 0;var heateorSssCopyLinkMessage = "Link copied.";var heateorSssUrlCountFetched = [], heateorSssSharesText = 'Shares', heateorSssShareText = 'Share';function heateorSssPopup(e) {window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}
</script>
<script src="https://www.stackpath.com/wp-content/plugins/sassy-social-share/public/js/sassy-social-share-public.js?ver=3.3.60" id="heateor_sss_sharing_js-js"></script>
<script src="https://www.stackpath.com/wp-content/themes/StackPath/build/main.js?ver=1.0.0" id="main-script-js"></script>
</body>

</html>
