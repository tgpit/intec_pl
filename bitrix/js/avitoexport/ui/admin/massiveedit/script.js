this.BX=this.BX||{},this.BX.AvitoExport=this.BX.AvitoExport||{},this.BX.AvitoExport.Ui=this.BX.AvitoExport.Ui||{},function(t){"use strict";class e{constructor(t,e={}){this.onActionDone=()=>{BX.closeWait(),this.modal().Close(),this.grid().reload(),this.releaseModal(),this.handleActionDone(!1),this.handleModalClose(!1)},this.onWindowClose=()=>{this.releaseModal(),this.handleActionDone(!1),this.handleModalClose(!1)},this.tableId=t,this.options=Object.assign({},this.constructor.defaults,e)}handleActionDone(t){BX[t?"addCustomEvent":"removeCustomEvent"]("avitoExportMassiveEditDone",this.onActionDone)}handleModalClose(t){BX[t?"addCustomEvent":"removeCustomEvent"](this.modal(),"onWindowClose",this.onWindowClose)}open(){this.modal().Show(),this.handleModalClose(!0),this.handleActionDone(!0)}modal(){return null==this._modal&&(this._modal=this.createModal()),this._modal}releaseModal(){this._modal=null}createModal(){return new BX.CAdminDialog({content_url:this.url(),content_post:{selected:this.selectedRows(),property:this.options.propertyId,iblockId:this.options.iblockId},width:800,height:500,resizable:!0,buttons:[BX.CAdminDialog.btnSave,BX.CAdminDialog.btnCancel]})}isForAllChecked(){var t,e,o;return null==(t=this.grid())||null==(e=t.getActionsPanel())||null==(o=e.getForAllCheckbox())?void 0:o.checked}selectedRows(){const t=this.grid().getRows().getSelectedIds(),e=this.options.prefixSelected;if(this.isForAllChecked())throw new Error(BX.message("AVITO_EXPORT_UI_ADMIN_MASSIVE_EDIT_FOR_ALL_NOT_SUPPORTED"));if(null==e)return t;for(let o in t)t.hasOwnProperty(o)&&(t[o]=e+t[o]);return t}url(){return`/bitrix/tools/avito.export/massiveedit/modal.php?bxpublic=Y&lang=${this.options.language}`}grid(){return BX.Main.gridManager.getById(this.tableId).instance}}e.defaults={propertyId:null,iblockId:null,language:null,prefixSelected:null},t.MassiveEdit=e,t.massiveEditOpen=function(t,o={}){try{new e(t,o).open()}catch(e){!function(t,e){BX.Main.gridManager.getById(t).instance.arParams.MESSAGES=[{TYPE:"ERROR",TEXT:e}],BX.onCustomEvent(window,"BX.Main.grid:paramsUpdated",[])}(t,e.message)}}}(this.BX.AvitoExport.Ui.Admin=this.BX.AvitoExport.Ui.Admin||{});
//# sourceMappingURL=script.js.map