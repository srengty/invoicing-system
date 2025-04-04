import{B as X,R as Y,z as D,H as u,ab as K,a7 as k,a6 as g,ap as L,a4 as p,aq as S,ar as O,as as _,a9 as Z,G as F,b as $,d as m,e as c,F as w,x as j,j as I,i as d,f as P,p as T,q as b,S as v,l as y,t as C,h as H,at as B,ao as U,I as G,n as ee,a as M,g as A,P as R,N as te}from"./app-DZTdZYTv.js";/* empty css                                                    */import{s as z}from"./index-Ch5aU8qY.js";import{s as q}from"./index-DRzkQ_aZ.js";var ne=({dt:t})=>`
.p-panelmenu {
    display: flex;
    flex-direction: column;
    gap: ${t("panelmenu.gap")};
}

.p-panelmenu-panel {
    background: ${t("panelmenu.panel.background")};
    border-width: ${t("panelmenu.panel.border.width")};
    border-style: solid;
    border-color: ${t("panelmenu.panel.border.color")};
    color: ${t("panelmenu.panel.color")};
    border-radius: ${t("panelmenu.panel.border.radius")};
    padding: ${t("panelmenu.panel.padding")};
}

.p-panelmenu-panel:first-child {
    border-width: ${t("panelmenu.panel.first.border.width")};
    border-start-start-radius: ${t("panelmenu.panel.first.top.border.radius")};
    border-start-end-radius: ${t("panelmenu.panel.first.top.border.radius")};
}

.p-panelmenu-panel:last-child {
    border-width: ${t("panelmenu.panel.last.border.width")};
    border-end-start-radius: ${t("panelmenu.panel.last.bottom.border.radius")};
    border-end-end-radius: ${t("panelmenu.panel.last.bottom.border.radius")};
}

.p-panelmenu-header {
    outline: 0 none;
}

.p-panelmenu-header-content {
    border-radius: ${t("panelmenu.item.border.radius")};
    transition: background ${t("panelmenu.transition.duration")}, color ${t("panelmenu.transition.duration")}, outline-color ${t("panelmenu.transition.duration")}, box-shadow ${t("panelmenu.transition.duration")};
    outline-color: transparent;
    color: ${t("panelmenu.item.color")};
}

.p-panelmenu-header-link {
    display: flex;
    gap: ${t("panelmenu.item.gap")};
    padding: ${t("panelmenu.item.padding")};
    align-items: center;
    user-select: none;
    cursor: pointer;
    position: relative;
    text-decoration: none;
    color: inherit;
}

.p-panelmenu-header-icon,
.p-panelmenu-item-icon {
    color: ${t("panelmenu.item.icon.color")};
}

.p-panelmenu-submenu-icon {
    color: ${t("panelmenu.submenu.icon.color")};
}

.p-panelmenu-submenu-icon:dir(rtl) {
    transform: rotate(180deg);
}

.p-panelmenu-header:not(.p-disabled):focus-visible .p-panelmenu-header-content {
    background: ${t("panelmenu.item.focus.background")};
    color: ${t("panelmenu.item.focus.color")};
}

.p-panelmenu-header:not(.p-disabled):focus-visible .p-panelmenu-header-content .p-panelmenu-header-icon {
    color: ${t("panelmenu.item.icon.focus.color")};
}

.p-panelmenu-header:not(.p-disabled):focus-visible .p-panelmenu-header-content .p-panelmenu-submenu-icon {
    color: ${t("panelmenu.submenu.icon.focus.color")};
}

.p-panelmenu-header:not(.p-disabled) .p-panelmenu-header-content:hover {
    background: ${t("panelmenu.item.focus.background")};
    color: ${t("panelmenu.item.focus.color")};
}

.p-panelmenu-header:not(.p-disabled) .p-panelmenu-header-content:hover .p-panelmenu-header-icon {
    color: ${t("panelmenu.item.icon.focus.color")};
}

.p-panelmenu-header:not(.p-disabled) .p-panelmenu-header-content:hover .p-panelmenu-submenu-icon {
    color: ${t("panelmenu.submenu.icon.focus.color")};
}

.p-panelmenu-submenu {
    margin: 0;
    padding: 0 0 0 ${t("panelmenu.submenu.indent")};
    outline: 0;
    list-style: none;
}

.p-panelmenu-submenu:dir(rtl) {
    padding: 0 ${t("panelmenu.submenu.indent")} 0 0;
}

.p-panelmenu-item-link {
    display: flex;
    gap: ${t("panelmenu.item.gap")};
    padding: ${t("panelmenu.item.padding")};
    align-items: center;
    user-select: none;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}

.p-panelmenu-item-label {
    line-height: 1;
}

.p-panelmenu-item-content {
    border-radius: ${t("panelmenu.item.border.radius")};
    transition: background ${t("panelmenu.transition.duration")}, color ${t("panelmenu.transition.duration")}, outline-color ${t("panelmenu.transition.duration")}, box-shadow ${t("panelmenu.transition.duration")};
    color: ${t("panelmenu.item.color")};
    outline-color: transparent;
}

.p-panelmenu-item.p-focus > .p-panelmenu-item-content {
    background: ${t("panelmenu.item.focus.background")};
    color: ${t("panelmenu.item.focus.color")};
}

.p-panelmenu-item.p-focus > .p-panelmenu-item-content .p-panelmenu-item-icon {
    color: ${t("panelmenu.item.focus.color")};
}

.p-panelmenu-item.p-focus > .p-panelmenu-item-content .p-panelmenu-submenu-icon {
    color: ${t("panelmenu.submenu.icon.focus.color")};
}

.p-panelmenu-item:not(.p-disabled) > .p-panelmenu-item-content:hover {
    background: ${t("panelmenu.item.focus.background")};
    color: ${t("panelmenu.item.focus.color")};
}

.p-panelmenu-item:not(.p-disabled) > .p-panelmenu-item-content:hover .p-panelmenu-item-icon {
    color: ${t("panelmenu.item.icon.focus.color")};
}

.p-panelmenu-item:not(.p-disabled) > .p-panelmenu-item-content:hover .p-panelmenu-submenu-icon {
    color: ${t("panelmenu.submenu.icon.focus.color")};
}
`,ae={root:"p-panelmenu p-component",panel:"p-panelmenu-panel",header:function(e){var n=e.instance,i=e.item;return["p-panelmenu-header",{"p-panelmenu-header-active":n.isItemActive(i)&&!!i.items,"p-disabled":n.isItemDisabled(i)}]},headerContent:"p-panelmenu-header-content",headerLink:"p-panelmenu-header-link",headerIcon:"p-panelmenu-header-icon",headerLabel:"p-panelmenu-header-label",contentContainer:"p-panelmenu-content-container",content:"p-panelmenu-content",rootList:"p-panelmenu-root-list",item:function(e){var n=e.instance,i=e.processedItem;return["p-panelmenu-item",{"p-focus":n.isItemFocused(i),"p-disabled":n.isItemDisabled(i)}]},itemContent:"p-panelmenu-item-content",itemLink:"p-panelmenu-item-link",itemIcon:"p-panelmenu-item-icon",itemLabel:"p-panelmenu-item-label",submenuIcon:"p-panelmenu-submenu-icon",submenu:"p-panelmenu-submenu",separator:"p-menuitem-separator"},ie=X.extend({name:"panelmenu",style:ne,classes:ae}),re={name:"BasePanelMenu",extends:D,props:{model:{type:Array,default:null},expandedKeys:{type:Object,default:null},multiple:{type:Boolean,default:!1},tabindex:{type:Number,default:0}},style:ie,provide:function(){return{$pcPanelMenu:this,$parentInstance:this}}},W={name:"PanelMenuSub",hostName:"PanelMenu",extends:D,emits:["item-toggle","item-mousemove"],props:{panelId:{type:String,default:null},focusedItemId:{type:String,default:null},items:{type:Array,default:null},level:{type:Number,default:0},templates:{type:Object,default:null},activeItemPath:{type:Object,default:null},tabindex:{type:Number,default:-1}},methods:{getItemId:function(e){return"".concat(this.panelId,"_").concat(e.key)},getItemKey:function(e){return this.getItemId(e)},getItemProp:function(e,n,i){return e&&e.item?S(e.item[n],i):void 0},getItemLabel:function(e){return this.getItemProp(e,"label")},getPTOptions:function(e,n,i){return this.ptm(e,{context:{item:n.item,index:i,active:this.isItemActive(n),focused:this.isItemFocused(n),disabled:this.isItemDisabled(n)}})},isItemActive:function(e){return this.activeItemPath.some(function(n){return n.key===e.key})},isItemVisible:function(e){return this.getItemProp(e,"visible")!==!1},isItemDisabled:function(e){return this.getItemProp(e,"disabled")},isItemFocused:function(e){return this.focusedItemId===this.getItemId(e)},isItemGroup:function(e){return p(e.items)},onItemClick:function(e,n){this.getItemProp(n,"command",{originalEvent:e,item:n.item}),this.$emit("item-toggle",{processedItem:n,expanded:!this.isItemActive(n)})},onItemToggle:function(e){this.$emit("item-toggle",e)},onItemMouseMove:function(e,n){this.$emit("item-mousemove",{originalEvent:e,processedItem:n})},getAriaSetSize:function(){var e=this;return this.items.filter(function(n){return e.isItemVisible(n)&&!e.getItemProp(n,"separator")}).length},getAriaPosInset:function(e){var n=this;return e-this.items.slice(0,e).filter(function(i){return n.isItemVisible(i)&&n.getItemProp(i,"separator")}).length+1},getMenuItemProps:function(e,n){return{action:u({class:this.cx("itemLink"),tabindex:-1},this.getPTOptions("itemLink",e,n)),icon:u({class:[this.cx("itemIcon"),this.getItemProp(e,"icon")]},this.getPTOptions("itemIcon",e,n)),label:u({class:this.cx("itemLabel")},this.getPTOptions("itemLabel",e,n)),submenuicon:u({class:this.cx("submenuIcon")},this.getPTOptions("submenuicon",e,n))}}},components:{ChevronRightIcon:q,ChevronDownIcon:z},directives:{ripple:Y}},se=["tabindex"],oe=["id","aria-label","aria-expanded","aria-level","aria-setsize","aria-posinset","data-p-focused","data-p-disabled"],le=["onClick","onMousemove"],ue=["href","target"];function ce(t,e,n,i,r,a){var l=F("PanelMenuSub",!0),o=$("ripple");return c(),m("ul",{class:y(t.cx("submenu")),tabindex:n.tabindex},[(c(!0),m(w,null,j(n.items,function(s,f){return c(),m(w,{key:a.getItemKey(s)},[a.isItemVisible(s)&&!a.getItemProp(s,"separator")?(c(),m("li",u({key:0,id:a.getItemId(s),class:[t.cx("item",{processedItem:s}),a.getItemProp(s,"class")],style:a.getItemProp(s,"style"),role:"treeitem","aria-label":a.getItemLabel(s),"aria-expanded":a.isItemGroup(s)?a.isItemActive(s):void 0,"aria-level":n.level+1,"aria-setsize":a.getAriaSetSize(),"aria-posinset":a.getAriaPosInset(f),ref_for:!0},a.getPTOptions("item",s,f),{"data-p-focused":a.isItemFocused(s),"data-p-disabled":a.isItemDisabled(s)}),[d("div",u({class:t.cx("itemContent"),onClick:function(E){return a.onItemClick(E,s)},onMousemove:function(E){return a.onItemMouseMove(E,s)},ref_for:!0},a.getPTOptions("itemContent",s,f)),[n.templates.item?(c(),b(v(n.templates.item),{key:1,item:s.item,root:!1,active:a.isItemActive(s),hasSubmenu:a.isItemGroup(s),label:a.getItemLabel(s),props:a.getMenuItemProps(s,f)},null,8,["item","active","hasSubmenu","label","props"])):T((c(),m("a",u({key:0,href:a.getItemProp(s,"url"),class:t.cx("itemLink"),target:a.getItemProp(s,"target"),tabindex:"-1",ref_for:!0},a.getPTOptions("itemLink",s,f)),[a.isItemGroup(s)?(c(),m(w,{key:0},[n.templates.submenuicon?(c(),b(v(n.templates.submenuicon),u({key:0,class:t.cx("submenuIcon"),active:a.isItemActive(s),ref_for:!0},a.getPTOptions("submenuIcon",s,f)),null,16,["class","active"])):(c(),b(v(a.isItemActive(s)?"ChevronDownIcon":"ChevronRightIcon"),u({key:1,class:t.cx("submenuIcon"),ref_for:!0},a.getPTOptions("submenuIcon",s,f)),null,16,["class"]))],64)):I("",!0),n.templates.itemicon?(c(),b(v(n.templates.itemicon),{key:1,item:s.item,class:y(t.cx("itemIcon"))},null,8,["item","class"])):a.getItemProp(s,"icon")?(c(),m("span",u({key:2,class:[t.cx("itemIcon"),a.getItemProp(s,"icon")],ref_for:!0},a.getPTOptions("itemIcon",s,f)),null,16)):I("",!0),d("span",u({class:t.cx("itemLabel"),ref_for:!0},a.getPTOptions("itemLabel",s,f)),C(a.getItemLabel(s)),17)],16,ue)),[[o]])],16,le),P(U,u({name:"p-toggleable-content",ref_for:!0},t.ptm("transition")),{default:H(function(){return[T(d("div",u({class:t.cx("contentContainer"),ref_for:!0},t.ptm("contentContainer")),[a.isItemVisible(s)&&a.isItemGroup(s)?(c(),b(l,u({key:0,id:a.getItemId(s)+"_list",role:"group",panelId:n.panelId,focusedItemId:n.focusedItemId,items:s.items,level:n.level+1,templates:n.templates,activeItemPath:n.activeItemPath,onItemToggle:a.onItemToggle,onItemMousemove:e[0]||(e[0]=function(h){return t.$emit("item-mousemove",h)}),pt:t.pt,unstyled:t.unstyled,ref_for:!0},t.ptm("submenu")),null,16,["id","panelId","focusedItemId","items","level","templates","activeItemPath","onItemToggle","pt","unstyled"])):I("",!0)],16),[[B,a.isItemActive(s)]])]}),_:2},1040)],16,oe)):I("",!0),a.isItemVisible(s)&&a.getItemProp(s,"separator")?(c(),m("li",u({key:1,style:a.getItemProp(s,"style"),class:[t.cx("separator"),a.getItemProp(s,"class")],role:"separator",ref_for:!0},t.ptm("separator")),null,16)):I("",!0)],64)}),128))],10,se)}W.render=ce;function de(t,e){return he(t)||pe(t,e)||fe(t,e)||me()}function me(){throw new TypeError(`Invalid attempt to destructure non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function fe(t,e){if(t){if(typeof t=="string")return V(t,e);var n={}.toString.call(t).slice(8,-1);return n==="Object"&&t.constructor&&(n=t.constructor.name),n==="Map"||n==="Set"?Array.from(t):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?V(t,e):void 0}}function V(t,e){(e==null||e>t.length)&&(e=t.length);for(var n=0,i=Array(e);n<e;n++)i[n]=t[n];return i}function pe(t,e){var n=t==null?null:typeof Symbol<"u"&&t[Symbol.iterator]||t["@@iterator"];if(n!=null){var i,r,a,l,o=[],s=!0,f=!1;try{if(a=(n=n.call(t)).next,e!==0)for(;!(s=(i=a.call(n)).done)&&(o.push(i.value),o.length!==e);s=!0);}catch(h){f=!0,r=h}finally{try{if(!s&&n.return!=null&&(l=n.return(),Object(l)!==l))return}finally{if(f)throw r}}return o}}function he(t){if(Array.isArray(t))return t}var Q={name:"PanelMenuList",hostName:"PanelMenu",extends:D,emits:["item-toggle","header-focus"],props:{panelId:{type:String,default:null},items:{type:Array,default:null},templates:{type:Object,default:null},expandedKeys:{type:Object,default:null}},searchTimeout:null,searchValue:null,data:function(){return{focused:!1,focusedItem:null,activeItemPath:[]}},watch:{expandedKeys:function(e){this.autoUpdateActiveItemPath(e)}},created:function(){this.autoUpdateActiveItemPath(this.expandedKeys)},methods:{getItemProp:function(e,n){return e&&e.item?S(e.item[n]):void 0},getItemLabel:function(e){return this.getItemProp(e,"label")},isItemVisible:function(e){return this.getItemProp(e,"visible")!==!1},isItemDisabled:function(e){return this.getItemProp(e,"disabled")},isItemActive:function(e){return this.activeItemPath.some(function(n){return n.key===e.parentKey})},isItemGroup:function(e){return p(e.items)},onFocus:function(e){this.focused=!0,this.focusedItem=this.focusedItem||(this.isElementInPanel(e,e.relatedTarget)?this.findFirstItem():this.findLastItem())},onBlur:function(){this.focused=!1,this.focusedItem=null,this.searchValue=""},onKeyDown:function(e){var n=e.metaKey||e.ctrlKey;switch(e.code){case"ArrowDown":this.onArrowDownKey(e);break;case"ArrowUp":this.onArrowUpKey(e);break;case"ArrowLeft":this.onArrowLeftKey(e);break;case"ArrowRight":this.onArrowRightKey(e);break;case"Home":this.onHomeKey(e);break;case"End":this.onEndKey(e);break;case"Space":this.onSpaceKey(e);break;case"Enter":case"NumpadEnter":this.onEnterKey(e);break;case"Escape":case"Tab":case"PageDown":case"PageUp":case"Backspace":case"ShiftLeft":case"ShiftRight":break;default:!n&&Z(e.key)&&this.searchItems(e,e.key);break}},onArrowDownKey:function(e){var n=p(this.focusedItem)?this.findNextItem(this.focusedItem):this.findFirstItem();this.changeFocusedItem({originalEvent:e,processedItem:n,focusOnNext:!0}),e.preventDefault()},onArrowUpKey:function(e){var n=p(this.focusedItem)?this.findPrevItem(this.focusedItem):this.findLastItem();this.changeFocusedItem({originalEvent:e,processedItem:n,selfCheck:!0}),e.preventDefault()},onArrowLeftKey:function(e){var n=this;if(p(this.focusedItem)){var i=this.activeItemPath.some(function(r){return r.key===n.focusedItem.key});i?this.activeItemPath=this.activeItemPath.filter(function(r){return r.key!==n.focusedItem.key}):this.focusedItem=p(this.focusedItem.parent)?this.focusedItem.parent:this.focusedItem,e.preventDefault()}},onArrowRightKey:function(e){var n=this;if(p(this.focusedItem)){var i=this.isItemGroup(this.focusedItem);if(i){var r=this.activeItemPath.some(function(a){return a.key===n.focusedItem.key});r?this.onArrowDownKey(e):(this.activeItemPath=this.activeItemPath.filter(function(a){return a.parentKey!==n.focusedItem.parentKey}),this.activeItemPath.push(this.focusedItem))}e.preventDefault()}},onHomeKey:function(e){this.changeFocusedItem({originalEvent:e,processedItem:this.findFirstItem(),allowHeaderFocus:!1}),e.preventDefault()},onEndKey:function(e){this.changeFocusedItem({originalEvent:e,processedItem:this.findLastItem(),focusOnNext:!0,allowHeaderFocus:!1}),e.preventDefault()},onEnterKey:function(e){if(p(this.focusedItem)){var n=g(this.$el,'li[id="'.concat("".concat(this.focusedItemId),'"]')),i=n&&(g(n,'[data-pc-section="itemlink"]')||g(n,"a,button"));i?i.click():n&&n.click()}e.preventDefault()},onSpaceKey:function(e){this.onEnterKey(e)},onItemToggle:function(e){var n=e.processedItem,i=e.expanded;this.expandedKeys?this.$emit("item-toggle",{item:n.item,expanded:i}):(this.activeItemPath=this.activeItemPath.filter(function(r){return r.parentKey!==n.parentKey}),i&&this.activeItemPath.push(n)),this.focusedItem=n,K(this.$el)},onItemMouseMove:function(e){this.focused&&(this.focusedItem=e.processedItem)},isElementInPanel:function(e,n){var i=e.currentTarget.closest('[data-pc-section="panel"]');return i&&i.contains(n)},isItemMatched:function(e){var n;return this.isValidItem(e)&&((n=this.getItemLabel(e))===null||n===void 0?void 0:n.toLocaleLowerCase(this.searchLocale).startsWith(this.searchValue.toLocaleLowerCase(this.searchLocale)))},isVisibleItem:function(e){return!!e&&(e.level===0||this.isItemActive(e))&&this.isItemVisible(e)},isValidItem:function(e){return!!e&&!this.isItemDisabled(e)&&!this.getItemProp(e,"separator")},findFirstItem:function(){var e=this;return this.visibleItems.find(function(n){return e.isValidItem(n)})},findLastItem:function(){var e=this;return _(this.visibleItems,function(n){return e.isValidItem(n)})},findNextItem:function(e){var n=this,i=this.visibleItems.findIndex(function(a){return a.key===e.key}),r=i<this.visibleItems.length-1?this.visibleItems.slice(i+1).find(function(a){return n.isValidItem(a)}):void 0;return r||e},findPrevItem:function(e){var n=this,i=this.visibleItems.findIndex(function(a){return a.key===e.key}),r=i>0?_(this.visibleItems.slice(0,i),function(a){return n.isValidItem(a)}):void 0;return r||e},searchItems:function(e,n){var i=this;this.searchValue=(this.searchValue||"")+n;var r=null,a=!1;if(p(this.focusedItem)){var l=this.visibleItems.findIndex(function(o){return o.key===i.focusedItem.key});r=this.visibleItems.slice(l).find(function(o){return i.isItemMatched(o)}),r=O(r)?this.visibleItems.slice(0,l).find(function(o){return i.isItemMatched(o)}):r}else r=this.visibleItems.find(function(o){return i.isItemMatched(o)});return p(r)&&(a=!0),O(r)&&O(this.focusedItem)&&(r=this.findFirstItem()),p(r)&&this.changeFocusedItem({originalEvent:e,processedItem:r,allowHeaderFocus:!1}),this.searchTimeout&&clearTimeout(this.searchTimeout),this.searchTimeout=setTimeout(function(){i.searchValue="",i.searchTimeout=null},500),a},changeFocusedItem:function(e){var n=e.originalEvent,i=e.processedItem,r=e.focusOnNext,a=e.selfCheck,l=e.allowHeaderFocus,o=l===void 0?!0:l;p(this.focusedItem)&&this.focusedItem.key!==i.key?(this.focusedItem=i,this.scrollInView()):o&&this.$emit("header-focus",{originalEvent:n,focusOnNext:r,selfCheck:a})},scrollInView:function(){var e=g(this.$el,'li[id="'.concat("".concat(this.focusedItemId),'"]'));e&&e.scrollIntoView&&e.scrollIntoView({block:"nearest",inline:"start"})},autoUpdateActiveItemPath:function(e){var n=this;this.activeItemPath=Object.entries(e||{}).reduce(function(i,r){var a=de(r,2),l=a[0],o=a[1];if(o){var s=n.findProcessedItemByItemKey(l);s&&i.push(s)}return i},[])},findProcessedItemByItemKey:function(e,n){var i=arguments.length>2&&arguments[2]!==void 0?arguments[2]:0;if(n=n||i===0&&this.processedItems,!n)return null;for(var r=0;r<n.length;r++){var a=n[r];if(this.getItemProp(a,"key")===e)return a;var l=this.findProcessedItemByItemKey(e,a.items,i+1);if(l)return l}},createProcessedItems:function(e){var n=this,i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:0,r=arguments.length>2&&arguments[2]!==void 0?arguments[2]:{},a=arguments.length>3&&arguments[3]!==void 0?arguments[3]:"",l=[];return e&&e.forEach(function(o,s){var f=(a!==""?a+"_":"")+s,h={item:o,index:s,level:i,key:f,parent:r,parentKey:a};h.items=n.createProcessedItems(o.items,i+1,h,f),l.push(h)}),l},flatItems:function(e){var n=this,i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:[];return e&&e.forEach(function(r){n.isVisibleItem(r)&&(i.push(r),n.flatItems(r.items,i))}),i}},computed:{processedItems:function(){return this.createProcessedItems(this.items||[])},visibleItems:function(){return this.flatItems(this.processedItems)},focusedItemId:function(){return p(this.focusedItem)?"".concat(this.panelId,"_").concat(this.focusedItem.key):null}},components:{PanelMenuSub:W}};function Ie(t,e,n,i,r,a){var l=F("PanelMenuSub");return c(),b(l,u({id:n.panelId+"_list",class:t.cx("rootList"),role:"tree",tabindex:-1,"aria-activedescendant":r.focused?a.focusedItemId:void 0,panelId:n.panelId,focusedItemId:r.focused?a.focusedItemId:void 0,items:a.processedItems,templates:n.templates,activeItemPath:r.activeItemPath,onFocus:a.onFocus,onBlur:a.onBlur,onKeydown:a.onKeyDown,onItemToggle:a.onItemToggle,onItemMousemove:a.onItemMouseMove,pt:t.pt,unstyled:t.unstyled},t.ptm("rootList")),null,16,["id","class","aria-activedescendant","panelId","focusedItemId","items","templates","activeItemPath","onFocus","onBlur","onKeydown","onItemToggle","onItemMousemove","pt","unstyled"])}Q.render=Ie;function x(t){"@babel/helpers - typeof";return x=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},x(t)}function N(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter(function(r){return Object.getOwnPropertyDescriptor(t,r).enumerable})),n.push.apply(n,i)}return n}function ge(t){for(var e=1;e<arguments.length;e++){var n=arguments[e]!=null?arguments[e]:{};e%2?N(Object(n),!0).forEach(function(i){be(t,i,n[i])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):N(Object(n)).forEach(function(i){Object.defineProperty(t,i,Object.getOwnPropertyDescriptor(n,i))})}return t}function be(t,e,n){return(e=ve(e))in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function ve(t){var e=ye(t,"string");return x(e)=="symbol"?e:e+""}function ye(t,e){if(x(t)!="object"||!t)return t;var n=t[Symbol.toPrimitive];if(n!==void 0){var i=n.call(t,e);if(x(i)!="object")return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var J={name:"PanelMenu",extends:re,inheritAttrs:!1,emits:["update:expandedKeys","panel-open","panel-close"],data:function(){return{activeItem:null,activeItems:[]}},methods:{getItemProp:function(e,n){return e?S(e[n]):void 0},getItemLabel:function(e){return this.getItemProp(e,"label")},getPTOptions:function(e,n,i){return this.ptm(e,{context:{index:i,active:this.isItemActive(n),focused:this.isItemFocused(n),disabled:this.isItemDisabled(n)}})},isItemActive:function(e){return this.expandedKeys?this.expandedKeys[this.getItemProp(e,"key")]:this.multiple?this.activeItems.some(function(n){return k(e,n)}):k(e,this.activeItem)},isItemVisible:function(e){return this.getItemProp(e,"visible")!==!1},isItemDisabled:function(e){return this.getItemProp(e,"disabled")},isItemFocused:function(e){return k(e,this.activeItem)},isItemGroup:function(e){return p(e.items)},getPanelId:function(e){return"".concat(this.$id,"_").concat(e)},getPanelKey:function(e){return this.getPanelId(e)},getHeaderId:function(e){return"".concat(this.getPanelId(e),"_header")},getContentId:function(e){return"".concat(this.getPanelId(e),"_content")},onHeaderClick:function(e,n){if(this.isItemDisabled(n)){e.preventDefault();return}n.command&&n.command({originalEvent:e,item:n}),this.changeActiveItem(e,n),K(e.currentTarget)},onHeaderKeyDown:function(e,n){switch(e.code){case"ArrowDown":this.onHeaderArrowDownKey(e);break;case"ArrowUp":this.onHeaderArrowUpKey(e);break;case"Home":this.onHeaderHomeKey(e);break;case"End":this.onHeaderEndKey(e);break;case"Enter":case"NumpadEnter":case"Space":this.onHeaderEnterKey(e,n);break}},onHeaderArrowDownKey:function(e){var n=L(e.currentTarget,"data-p-active")===!0?g(e.currentTarget.nextElementSibling,'[data-pc-section="rootlist"]'):null;n?K(n):this.updateFocusedHeader({originalEvent:e,focusOnNext:!0}),e.preventDefault()},onHeaderArrowUpKey:function(e){var n=this.findPrevHeader(e.currentTarget.parentElement)||this.findLastHeader(),i=L(n,"data-p-active")===!0?g(n.nextElementSibling,'[data-pc-section="rootlist"]'):null;i?K(i):this.updateFocusedHeader({originalEvent:e,focusOnNext:!1}),e.preventDefault()},onHeaderHomeKey:function(e){this.changeFocusedHeader(e,this.findFirstHeader()),e.preventDefault()},onHeaderEndKey:function(e){this.changeFocusedHeader(e,this.findLastHeader()),e.preventDefault()},onHeaderEnterKey:function(e,n){var i=g(e.currentTarget,'[data-pc-section="headerlink"]');i?i.click():this.onHeaderClick(e,n),e.preventDefault()},findNextHeader:function(e){var n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,i=n?e:e.nextElementSibling,r=g(i,'[data-pc-section="header"]');return r?L(r,"data-p-disabled")?this.findNextHeader(r.parentElement):r:null},findPrevHeader:function(e){var n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,i=n?e:e.previousElementSibling,r=g(i,'[data-pc-section="header"]');return r?L(r,"data-p-disabled")?this.findPrevHeader(r.parentElement):r:null},findFirstHeader:function(){return this.findNextHeader(this.$el.firstElementChild,!0)},findLastHeader:function(){return this.findPrevHeader(this.$el.lastElementChild,!0)},updateFocusedHeader:function(e){var n=e.originalEvent,i=e.focusOnNext,r=e.selfCheck,a=n.currentTarget.closest('[data-pc-section="panel"]'),l=r?g(a,'[data-pc-section="header"]'):i?this.findNextHeader(a):this.findPrevHeader(a);l?this.changeFocusedHeader(n,l):i?this.onHeaderHomeKey(n):this.onHeaderEndKey(n)},changeActiveItem:function(e,n){var i=arguments.length>2&&arguments[2]!==void 0?arguments[2]:!1;if(!this.isItemDisabled(n)){var r=this.isItemActive(n),a=r?"panel-close":"panel-open";this.activeItem=i?n:this.activeItem&&k(n,this.activeItem)?null:n,this.multiple&&(this.activeItems.some(function(l){return k(n,l)})?this.activeItems=this.activeItems.filter(function(l){return!k(n,l)}):this.activeItems.push(n)),this.changeExpandedKeys({item:n,expanded:!r}),this.$emit(a,{originalEvent:e,item:n})}},changeExpandedKeys:function(e){var n=e.item,i=e.expanded,r=i===void 0?!1:i;if(this.expandedKeys){var a=ge({},this.expandedKeys);r?a[n.key]=!0:delete a[n.key],this.$emit("update:expandedKeys",a)}},changeFocusedHeader:function(e,n){n&&K(n)},getMenuItemProps:function(e,n){return{icon:u({class:[this.cx("headerIcon"),this.getItemProp(e,"icon")]},this.getPTOptions("headerIcon",e,n)),label:u({class:this.cx("headerLabel")},this.getPTOptions("headerLabel",e,n))}}},components:{PanelMenuList:Q,ChevronRightIcon:q,ChevronDownIcon:z}},Pe=["id"],ke=["id","tabindex","aria-label","aria-expanded","aria-controls","aria-disabled","onClick","onKeydown","data-p-active","data-p-disabled"],we=["href"],Ke=["id","aria-labelledby"];function Ae(t,e,n,i,r,a){var l=F("PanelMenuList");return c(),m("div",u({id:t.$id,class:t.cx("root")},t.ptmi("root")),[(c(!0),m(w,null,j(t.model,function(o,s){return c(),m(w,{key:a.getPanelKey(s)},[a.isItemVisible(o)?(c(),m("div",u({key:0,style:a.getItemProp(o,"style"),class:[t.cx("panel"),a.getItemProp(o,"class")],ref_for:!0},t.ptm("panel")),[d("div",u({id:a.getHeaderId(s),class:[t.cx("header",{item:o}),a.getItemProp(o,"headerClass")],tabindex:a.isItemDisabled(o)?-1:t.tabindex,role:"button","aria-label":a.getItemLabel(o),"aria-expanded":a.isItemActive(o),"aria-controls":a.getContentId(s),"aria-disabled":a.isItemDisabled(o),onClick:function(h){return a.onHeaderClick(h,o)},onKeydown:function(h){return a.onHeaderKeyDown(h,o)},ref_for:!0},a.getPTOptions("header",o,s),{"data-p-active":a.isItemActive(o),"data-p-disabled":a.isItemDisabled(o)}),[d("div",u({class:t.cx("headerContent"),ref_for:!0},a.getPTOptions("headerContent",o,s)),[t.$slots.item?(c(),b(v(t.$slots.item),{key:1,item:o,root:!0,active:a.isItemActive(o),hasSubmenu:a.isItemGroup(o),label:a.getItemLabel(o),props:a.getMenuItemProps(o,s)},null,8,["item","active","hasSubmenu","label","props"])):(c(),m("a",u({key:0,href:a.getItemProp(o,"url"),class:t.cx("headerLink"),tabindex:-1,ref_for:!0},a.getPTOptions("headerLink",o,s)),[a.getItemProp(o,"items")?G(t.$slots,"submenuicon",{key:0,active:a.isItemActive(o)},function(){return[(c(),b(v(a.isItemActive(o)?"ChevronDownIcon":"ChevronRightIcon"),u({class:t.cx("submenuIcon"),ref_for:!0},a.getPTOptions("submenuIcon",o,s)),null,16,["class"]))]}):I("",!0),t.$slots.headericon?(c(),b(v(t.$slots.headericon),{key:1,item:o,class:y([t.cx("headerIcon"),a.getItemProp(o,"icon")])},null,8,["item","class"])):a.getItemProp(o,"icon")?(c(),m("span",u({key:2,class:[t.cx("headerIcon"),a.getItemProp(o,"icon")],ref_for:!0},a.getPTOptions("headerIcon",o,s)),null,16)):I("",!0),d("span",u({class:t.cx("headerLabel"),ref_for:!0},a.getPTOptions("headerLabel",o,s)),C(a.getItemLabel(o)),17)],16,we))],16)],16,ke),P(U,u({name:"p-toggleable-content",ref_for:!0},t.ptm("transition")),{default:H(function(){return[T(d("div",u({id:a.getContentId(s),class:t.cx("contentContainer"),role:"region","aria-labelledby":a.getHeaderId(s),ref_for:!0},t.ptm("contentContainer")),[a.getItemProp(o,"items")?(c(),m("div",u({key:0,class:t.cx("content"),ref_for:!0},t.ptm("content")),[P(l,{panelId:a.getPanelId(s),items:a.getItemProp(o,"items"),templates:t.$slots,expandedKeys:t.expandedKeys,onItemToggle:a.changeExpandedKeys,onHeaderFocus:a.updateFocusedHeader,pt:t.pt,unstyled:t.unstyled},null,8,["panelId","items","templates","expandedKeys","onItemToggle","onHeaderFocus","pt","unstyled"])],16)):I("",!0)],16,Ke),[[B,a.isItemActive(o)]])]}),_:2},1040)],16)):I("",!0)],64)}),128))],16,Pe)}J.render=Ae;const He={class:"card flex justify-center pr-1"},xe={key:1,class:"ml-auto rounded text-muted-color text-xl p-1 w-8"},Le={__name:"MainNavTree",setup(t){const e=ee(),n=M([{key:1,label:"Dashboard",href:"/",icon:"pi pi-home",shortcut:">",badge:null,items:[{label:"Compose",href:"/compose",icon:"pi pi-file-edit",shortcut:"⌘+N"}]},{key:2,label:"Quotations",href:"/quotations",icon:"pi pi-chart-bar",shortcut:"⌘+R",items:[{label:"New quotation",href:"/quotations/create",icon:"pi pi-chart-line",badge:0}]},{key:3,label:"Agreements",href:"/agreements",icon:"pi pi-user",shortcut:">",items:[{label:"Create Agreement",href:"/agreements/create",icon:"pi pi-plus",shortcut:"⌘+O"}]},{key:4,label:"Invoices",href:"/invoices",icon:"pi pi-money-bill",shortcut:"⌘+W",items:[{label:"Create Invoice",href:"/invoices/create",icon:"pi pi-cog",shortcut:"⌘+O"}]},{key:5,label:"Settings",href:"/settings",icon:"pi pi-cog",shortcut:"⌘+W",items:[{label:"Customers",href:"/settings/customers",icon:"pi pi-user",shortcut:"⌘+W"},{label:"Items",href:"/settings/products",icon:"pi pi-box",shortcut:"⌘+P"},{label:"Customer Categories",href:"/settings/customer-categories",icon:"pi pi-box",shortcut:"⌘+P"},{label:"Product Categories",href:"/settings/product-categories",icon:"pi pi-box",shortcut:"⌘+P"}]}]),i=M(n.value.reduce((r,a)=>(e.url.startsWith(a.href)&&a.href!=="/"&&(r[a.key]=!0),r),{}));return(r,a)=>(c(),m(w,null,[a[2]||(a[2]=d("div",{class:"card flex justify-center dark:bg-white"},null,-1)),a[3]||(a[3]=d("div",{class:"card flex justify-center pr-1"},null,-1)),a[4]||(a[4]=d("div",{class:"card flex justify-center dark:bg-white"},null,-1)),d("div",He,[P(A(J),{expandedKeys:i.value,"onUpdate:expandedKeys":a[0]||(a[0]=l=>i.value=l),model:n.value,class:"w-full md:w-80 text-xs",pt:{panel:{class:"border-0 bg-transparent p-0"}},multiple:""},{item:H(({item:l})=>[d("div",{class:y(["flex items-center cursor-pointer group",{active:A(e).url==l.href}])},[P(A(R),{href:l.href,class:"flex items-center px-4 py-2 group"},{default:H(()=>[d("span",{class:y(["rounded-full me-2 w-8 h-8 bg-green-300 flex items-center justify-center",{hidden:!l.id}])},C(l.id),3),d("span",{class:y([l.icon,"text-primary group-hover:text-inherit"])},null,2),d("span",{class:y(["ml-2",{"font-semibold":l.items}])},C(l.label),3)]),_:2},1032,["href"]),l.badge?(c(),b(A(te),{key:0,class:"ml-auto",value:l.badge},null,8,["value"])):I("",!0),l.shortcut?(c(),m("span",xe,a[1]||(a[1]=[d("span",{class:"pi pi-angle-right"},null,-1)]))):I("",!0)],2)]),_:1},8,["expandedKeys","model"])])],64))}},Ce={class:"flex min-h-screen flex-col items-center bg-gray-100 sm:justify-center dark:bg-gray-100"},Ee={class:"w-full h-screen overflow-y-auto bg-white shadow-md sm:rounded-lg dark:bg-gray-800 grid grid-cols-1 md:grid-cols-6"},Oe={class:"row-span-2 bg-slate-100 dark:bg-slate-100"},Te={class:"flex flex-col items-center justify-center py-2 pb-5 mb-3"},De={class:"md:col-span-5"},Ve={__name:"GuestLayout",setup(t){return(e,n)=>(c(),m("div",Ce,[d("div",Ee,[d("div",Oe,[d("div",Te,[P(A(R),{href:"/"},{default:H(()=>n[0]||(n[0]=[d("img",{src:"/logo.png",alt:"Logo",class:"h-20 w-20"},null,-1)])),_:1}),n[1]||(n[1]=d("div",{class:"text-sm"},[d("p",null,"Institute of Technology of Cambodia")],-1))]),P(Le)]),d("div",De,[G(e.$slots,"default")])])]))}};export{Ve as _};
