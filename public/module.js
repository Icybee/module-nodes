Brickrouge.Widget.AdjustNode=new Class({Implements:[Options,Events],options:{adjust:"adjust-node",constructor:"nodes"},initialize:function(a,b){this.element=document.id(a),this.nid=null,this.fetchResultsOperation=null,this.setOptions(b);var c=this.attachSearch(),d=this.getSelected();d&&(this.nid=d.get("data-nid")),this.element.addEvent("click:relay(.records [data-nid])",function(a,b){a.stop();var c=this.getSelected();c&&c.getParent("li").removeClass("selected"),b.getParent("li").addClass("selected"),this.nid=b.get("data-nid"),this.fireEvent("select",{target:this,selected:b,event:a}),this.fireEvent("change",{target:this,selected:b,event:a})}.bind(this)),this.element.addEvent("click:relay(.pagination a)",function(a,b){a.stop();var d=b.get("href").split("#")[1];this.fetchResults({page:d,search:c?c.value:null,selected:this.getValue()})}.bind(this))},attachSearch:function(){var a=this.element.getElement("input.search"),b=null;return a.onsubmit=function(){return!1},a.addEvent("keyup",function(a){"esc"==a.key&&(a.target.value="");var c=a.target.value;c!=b&&this.fetchResults({search:c,selected:this.getValue()}),b=c}.bind(this)),a},fetchResults:function(a){this.fetchResultsOperation||(this.fetchResultsOperation=new Request.Element({url:"widgets/"+this.options.adjust+"/results",onSuccess:function(a,b){a.replaces(this.element.getElement(".results")),Brickrouge.updateDocument(a),this.fireEvent("results",{target:this,response:b})}.bind(this)})),a.constructor=this.options.constructor,this.fetchResultsOperation.get(a)},getValue:function(){return this.nid},setValue:function(a){a!=this.getValue()&&(this.nid=a,this.fetchResults({selected:a}))},getSelected:function(){return this.element.getElement(".records li.selected [data-nid]")},setSelected:function(a){this.setValue(a)}}),Brickrouge.Widget.PopNode=new Class({Extends:Brickrouge.Widget.Spinner,Implements:[Options,Events],options:{placeholder:"Select an entry",constructor:"nodes",adjust:"adjust-node"},initialize:function(a,b){this.parent(a,b),this.opening=!1,this.popover=null,this.fetchAdjustOperation=new Request.Widget(this.options.adjust+"/popup",this.setupAdjust.bind(this))},open:function(){if(!this.opening){this.opening=!0;var a=this.getValue();return this.resetValue=a,this.popover?(this.popover.getAdjust().setValue(a),this.popover.show(),this.opening=!1,void 0):(this.fetchAdjustOperation.get({selected:a,constructor:this.options.constructor}),void 0)}},setupAdjust:function(a){this.popover=new Icybee.Widget.AdjustPopover(a,{anchor:this.element}),this.popover.show(),this.opening=!1,this.popover.adjust.addEvent("change",this.change.bind(this)),this.popover.addEvent("action",this.onAction.bind(this))},onAction:function(a){switch(a.action){case"cancel":this.cancel();break;case"remove":this.remove();case"use":this.use()}this.popover.hide()},change:function(a){this.setValue(a.selected.get("data-nid"))},cancel:function(){this.setValue(this.resetValue)},remove:function(){this.setValue("")},use:function(){this.element.fireEvent("change",{})},reset:function(){}}),Brickrouge.Widget.TitleSlugCombo=new Class({initialize:function(a){function j(a){a.stop(),i=!i,d.setStyle("display",i?"block":"none"),c.setStyle("display",i?"none":"inline"),f.setStyle("display",i?"inline":"none")}function k(){var b=h.get("value"),d=b?"text":"html";b?(b=b.shorten(),g.getParent("span").setStyle("display","inline")):(b=a.get("data-auto-label"),g.getParent("span").setStyle("display","none")),c.getElement("a").set(d,b)}this.element=a=document.id(a);var c=a.getElement(".slug-reminder"),d=a.getElement(".slug"),e=a.getElement('a[href$="slug-edit"'),f=a.getElement('a[href$="slug-collapse"]'),g=a.getElement('a[href$="slug-delete"]'),h=d.getElement("input"),i=!1;e.addEvent("click",j),f.addEvent("click",j),h.addEvent("change",k),g.addEvent("click",function(a){a.stop(),h.value="",h.fireEvent("change",{})}),k()}}),document.body.addEvent('click:relay(#manager [data-property="is_online"])',function(a,b){new Request.API({url:manager.destination+"/"+b.value+"/"+(b.checked?"online":"offline"),onFailure:function(){b.checked=!b.checked},onSuccess:function(){b.fireEvent("change",{})}}).post()});