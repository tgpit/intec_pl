{"version":3,"file":"attach.bundle.map.js","names":["exports","ui_designTokens","ui_icons_disk","ui_vue_directives_lazyload","im_model","im_lib_utils","ui_vue","AttachTypeDelimiter","property","name","component","props","config","type","Object","default","color","String","computed","styles","width","this","DELIMITER","SIZE","backgroundColor","COLOR","template","AttachTypeFile","methods","openLink","element","Utils","platform","openNewPage","LINK","file","FILE","NAME","extension","split","splice","size","fileName","maxLength","length","endWordLength","secondPart","substring","firstPart","trim","fileNameFull","fileSize","sizes","position","Math","round","$Bitrix","Loc","getMessage","fileIcon","FilesModel","getIconType","AttachLinks","event","eventData","isBitrixMobile","target","tagName","entity","id","hasOwnProperty","USER_ID","CHAT_ID","window","top","popupAngle","BX","MessengerTheme","isDark","messenger","openPopupExternalData","ID","navigator","userAgent","toLowerCase","includes","dialogId","concat","BXMobileApp","Events","postToComponent","AttachTypeGrid","mixins","created","maxCellWith","floor","min","screen","availWidth","availHeight","getWidth","DISPLAY","WIDTH","VALUE","getValueColor","getValue","text","decode","AttachTypeHtml","html","HTML","replace","AttachTypeImage","open","UI","Photo","show","photos","url","default_photo","getImageSize","height","maxWidth","aspectRatio","getElementSource","PREVIEW","lazyLoadCallback","style","offsetWidth","offsetHeight","styleFileSizes","image","HEIGHT","maxHeight","backgroundSize","styleBoxSizes","parseInt","AttachTypeLink","getImageConfig","IMAGE","getLinkName","getDescription","DESC","imageComponentName","components","babelHelpers","defineProperty","AttachTypeMessage","message","MESSAGE","AttachTypeRich","AttachTypeUser","getAvatarType","AVATAR","avatarType","AVATAR_TYPE","AttachTypes","AttachComponents","forEach","attachType","BitrixVue","baseColor","getComponentForBlock","block","_i","_AttachTypes","Messenger","Model","Lib"],"sources":["attach.bundle.js"],"mappings":"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"}