{"version":3,"file":"warehouse-master.bundle.map.js","names":["this","BX","Catalog","exports","ui_vue3","ui_vue3_vuex","main_core_events","main_core","catalog_storeUse","WarehouseSection","props","title","String","description","iconType","computed","getIconClass","_sectionIconClasses$t","sectionIconClasses","documents","crm","mobile","$props","template","ownKeys","object","enumerableOnly","keys","Object","getOwnPropertySymbols","symbols","filter","sym","getOwnPropertyDescriptor","enumerable","push","apply","_objectSpread","target","i","arguments","length","source","forEach","key","babelHelpers","defineProperty","getOwnPropertyDescriptors","defineProperties","Content","components","sectionTitlePrefix","sectionDescriptionPrefix","getMobileBoxClass","result","getPreviewLang","mapGetters","ownKeys$1","_objectSpread$1","Footer","getButtonClass","classes","isLoading","isRestrictedAccess","isUsed","getHintClass","getButtonText","$Bitrix","Loc","getMessage","methods","openHelpdesk","top","Helper","show","onButtonClick","$emit","mapMutations","ButtonClickHandler","classCallCheck","hasErrors","isPlanRestricted","isUsed1C","isWithOrdersMode","createClass","value","handle","handleDisableInventoryManagement","handleEnableInventoryManagement","checkAccess","checkPlanRestriction","checkUsage1C","checkWithOrdersMode","showEnablePopup","showConfirmDisablePopup","StoreUse","DialogEnable","popup","showErrorPopup","options","DialogError","showPlanRestrictionSlider","UI","InfoHelper","dialogDisable","DialogDisable","disablePopup","helpArticleId","text","ownKeys$2","_objectSpread$2","_classPrivateMethodInitSpec","obj","privateSet","_checkPrivateRedeclaration","add","_classPrivateFieldInitSpec","privateMap","set","privateCollection","has","TypeError","_classPrivateMethodGet","receiver","fn","_application","WeakMap","_initStore","WeakSet","App","_props","writable","rootNode","document","getElementById","rootNodeId","store","_initStore2","call","attachTemplate","classPrivateFieldSet","BitrixVue","createApp","created","controller","Controller","mounted","EventEmitter","subscribe","EventType","disable","enableWithResetDocuments","enableWithoutReset","selectCostPriceAccountingMethod","handleAccountingMethodSelected","unmounted","unsubscribe","handleOnButtonClick","getButtonClickHandler","item","data","method","DialogCostPriceAccountingMethodSelection","METHOD_FIFO","METHOD_AVERAGE","setSelectedCostPriceAccountingMethod","closeSlider","slider","SidePanel","Instance","getTopSlider","close","setIsLoading","inventoryManagementDisabled","then","handleSuccessfulChanging","handleUnsuccessfulChanging","enable","_this","enableBy","inventoryManagementEnabled","_this2","inventoryManagementEnableWithResetDocuments","costPriceAccountingMethod","getSelectedCostPriceAccountingMethod","_this3","inventoryManagementEnableWithoutReset","getData","response","errors","Notification","Center","notify","content","Text","encode","message","classPrivateFieldGet","use","mount","settingsStore","state","getters","selectedCostPriceAccountingMethod","getInventoryManagementSource","inventoryManagementSource","previewLang","mutations","createStore","WarehouseMaster","Vue3","Vuex","Event"],"sources":["warehouse-master.bundle.js"],"mappings":"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"}