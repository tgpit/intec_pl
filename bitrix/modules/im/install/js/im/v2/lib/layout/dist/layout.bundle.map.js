{"version":3,"file":"layout.bundle.map.js","names":["this","BX","Messenger","v2","exports","main_core_events","im_v2_application_core","im_v2_lib_localStorage","im_v2_lib_logger","im_v2_const","_instance","babelHelpers","classPrivateFieldLooseKey","_lastOpenedElement","_onGoToMessageContext","_onDesktopReload","LayoutManager","static","classPrivateFieldLooseBase","getInstance","constructor","Object","defineProperty","value","_onDesktopReload2","_onGoToMessageContext2","writable","EventEmitter","subscribe","EventType","dialog","goToMessageContext","bind","desktop","onReload","async","config","entityId","setLastOpenedElement","name","Core","getStore","dispatch","getLayout","getters","saveCurrentLayout","currentLayout","LocalStorageManager","set","LocalStorageKey","layoutConfig","restoreLastLayout","get","Promise","resolve","Logger","warn","remove","setLayout","getLastOpenedElement","layoutName","_babelHelpers$classPr","destroy","unsubscribe","event","dialogId","messageId","getData","Layout","chat","contextId","Lib","Event","Application","Const"],"sources":["layout.bundle.js"],"mappings":"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"}