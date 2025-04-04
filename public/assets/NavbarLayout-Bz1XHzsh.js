import{B as E,z as C,L as U,d as m,e as u,I as v,q as y,j as g,H as o,t as B,l as S,S as x,ad as W,R as Q,a4 as P,a6 as z,a8 as F,ag as J,ah as X,aj as Y,aN as A,am as T,an as _,ab as L,ar as K,a9 as ee,aq as R,aO as te,G as $,b as ne,h as b,F as D,x as ie,i as k,p as re,ao as N,f as I,y as ae,J as V,n as se,c as oe,g as w}from"./app-DZTdZYTv.js";import{O as ue,e as le,f as de,s as ce}from"./index-BFVOh3Gg.js";import{s as me}from"./index-Ch5aU8qY.js";import{e as fe}from"./index-Dm6_jG8z.js";import{_ as pe}from"./_plugin-vue_export-helper-DlAUqK2U.js";var he=({dt:e})=>`
.p-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: ${e("avatar.width")};
    height: ${e("avatar.height")};
    font-size: ${e("avatar.font.size")};
    background: ${e("avatar.background")};
    color: ${e("avatar.color")};
    border-radius: ${e("avatar.border.radius")};
}

.p-avatar-image {
    background: transparent;
}

.p-avatar-circle {
    border-radius: 50%;
}

.p-avatar-circle img {
    border-radius: 50%;
}

.p-avatar-icon {
    font-size: ${e("avatar.icon.size")};
    width: ${e("avatar.icon.size")};
    height: ${e("avatar.icon.size")};
}

.p-avatar img {
    width: 100%;
    height: 100%;
}

.p-avatar-lg {
    width: ${e("avatar.lg.width")};
    height: ${e("avatar.lg.width")};
    font-size: ${e("avatar.lg.font.size")};
}

.p-avatar-lg .p-avatar-icon {
    font-size: ${e("avatar.lg.icon.size")};
    width: ${e("avatar.lg.icon.size")};
    height: ${e("avatar.lg.icon.size")};
}

.p-avatar-xl {
    width: ${e("avatar.xl.width")};
    height: ${e("avatar.xl.width")};
    font-size: ${e("avatar.xl.font.size")};
}

.p-avatar-xl .p-avatar-icon {
    font-size: ${e("avatar.xl.icon.size")};
    width: ${e("avatar.xl.icon.size")};
    height: ${e("avatar.xl.icon.size")};
}

.p-avatar-group {
    display: flex;
    align-items: center;
}

.p-avatar-group .p-avatar + .p-avatar {
    margin-inline-start: ${e("avatar.group.offset")};
}

.p-avatar-group .p-avatar {
    border: 2px solid ${e("avatar.group.border.color")};
}

.p-avatar-group .p-avatar-lg + .p-avatar-lg {
    margin-inline-start: ${e("avatar.lg.group.offset")};
}

.p-avatar-group .p-avatar-xl + .p-avatar-xl {
    margin-inline-start: ${e("avatar.xl.group.offset")};
}
`,be={root:function(t){var n=t.props;return["p-avatar p-component",{"p-avatar-image":n.image!=null,"p-avatar-circle":n.shape==="circle","p-avatar-lg":n.size==="large","p-avatar-xl":n.size==="xlarge"}]},label:"p-avatar-label",icon:"p-avatar-icon"},Ie=E.extend({name:"avatar",style:he,classes:be}),ve={name:"BaseAvatar",extends:C,props:{label:{type:String,default:null},icon:{type:String,default:null},image:{type:String,default:null},size:{type:String,default:"normal"},shape:{type:String,default:"square"},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:Ie,provide:function(){return{$pcAvatar:this,$parentInstance:this}}};function M(e){"@babel/helpers - typeof";return M=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},M(e)}function O(e,t,n){return(t=ge(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function ge(e){var t=ye(e,"string");return M(t)=="symbol"?t:t+""}function ye(e,t){if(M(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var r=n.call(e,t);if(M(r)!="object")return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var G={name:"Avatar",extends:ve,inheritAttrs:!1,emits:["error"],methods:{onError:function(t){this.$emit("error",t)}},computed:{dataP:function(){return U(O(O({},this.shape,this.shape),this.size,this.size))}}},ke=["aria-labelledby","aria-label","data-p"],Le=["data-p"],we=["data-p"],Pe=["src","alt","data-p"];function $e(e,t,n,r,s,i){return u(),m("div",o({class:e.cx("root"),"aria-labelledby":e.ariaLabelledby,"aria-label":e.ariaLabel},e.ptmi("root"),{"data-p":i.dataP}),[v(e.$slots,"default",{},function(){return[e.label?(u(),m("span",o({key:0,class:e.cx("label")},e.ptm("label"),{"data-p":i.dataP}),B(e.label),17,Le)):e.$slots.icon?(u(),y(x(e.$slots.icon),{key:1,class:S(e.cx("icon"))},null,8,["class"])):e.icon?(u(),m("span",o({key:2,class:[e.cx("icon"),e.icon]},e.ptm("icon"),{"data-p":i.dataP}),null,16,we)):e.image?(u(),m("img",o({key:3,src:e.image,alt:e.ariaLabel,onError:t[0]||(t[0]=function(){return i.onError&&i.onError.apply(i,arguments)})},e.ptm("image"),{"data-p":i.dataP}),null,16,Pe)):g("",!0)]})],16,ke)}G.render=$e;var Se=({dt:e})=>`
.p-tieredmenu {
    background: ${e("tieredmenu.background")};
    color: ${e("tieredmenu.color")};
    border: 1px solid ${e("tieredmenu.border.color")};
    border-radius: ${e("tieredmenu.border.radius")};
    min-width: 12.5rem;
}

.p-tieredmenu-root-list,
.p-tieredmenu-submenu {
    margin: 0;
    padding: ${e("tieredmenu.list.padding")};
    list-style: none;
    outline: 0 none;
    display: flex;
    flex-direction: column;
    gap: ${e("tieredmenu.list.gap")};
}

.p-tieredmenu-submenu {
    position: absolute;
    min-width: 100%;
    z-index: 1;
    background: ${e("tieredmenu.background")};
    color: ${e("tieredmenu.color")};
    border: 1px solid ${e("tieredmenu.border.color")};
    border-radius: ${e("tieredmenu.border.radius")};
    box-shadow: ${e("tieredmenu.shadow")};
}

.p-tieredmenu-item {
    position: relative;
}

.p-tieredmenu-item-content {
    transition: background ${e("tieredmenu.transition.duration")}, color ${e("tieredmenu.transition.duration")};
    border-radius: ${e("tieredmenu.item.border.radius")};
    color: ${e("tieredmenu.item.color")};
}

.p-tieredmenu-item-link {
    cursor: pointer;
    display: flex;
    align-items: center;
    text-decoration: none;
    overflow: hidden;
    position: relative;
    color: inherit;
    padding: ${e("tieredmenu.item.padding")};
    gap: ${e("tieredmenu.item.gap")};
    user-select: none;
    outline: 0 none;
}

.p-tieredmenu-item-label {
    line-height: 1;
}

.p-tieredmenu-item-icon {
    color: ${e("tieredmenu.item.icon.color")};
}

.p-tieredmenu-submenu-icon {
    color: ${e("tieredmenu.submenu.icon.color")};
    margin-left: auto;
    font-size: ${e("tieredmenu.submenu.icon.size")};
    width: ${e("tieredmenu.submenu.icon.size")};
    height: ${e("tieredmenu.submenu.icon.size")};
}

.p-tieredmenu-submenu-icon:dir(rtl) {
    margin-left: 0;
    margin-right: auto;
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content {
    color: ${e("tieredmenu.item.focus.color")};
    background: ${e("tieredmenu.item.focus.background")};
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content .p-tieredmenu-item-icon {
    color: ${e("tieredmenu.item.icon.focus.color")};
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    color: ${e("tieredmenu.submenu.icon.focus.color")};
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover {
    color: ${e("tieredmenu.item.focus.color")};
    background: ${e("tieredmenu.item.focus.background")};
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover .p-tieredmenu-item-icon {
    color: ${e("tieredmenu.item.icon.focus.color")};
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover .p-tieredmenu-submenu-icon {
    color: ${e("tieredmenu.submenu.icon.focus.color")};
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content {
    color: ${e("tieredmenu.item.active.color")};
    background: ${e("tieredmenu.item.active.background")};
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-item-icon {
    color: ${e("tieredmenu.item.icon.active.color")};
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    color: ${e("tieredmenu.submenu.icon.active.color")};
}

.p-tieredmenu-separator {
    border-block-start: 1px solid ${e("tieredmenu.separator.border.color")};
}

.p-tieredmenu-overlay {
    box-shadow: ${e("tieredmenu.shadow")};
}

.p-tieredmenu-enter-from,
.p-tieredmenu-leave-active {
    opacity: 0;
}

.p-tieredmenu-enter-active {
    transition: opacity 250ms;
}

.p-tieredmenu-mobile .p-tieredmenu-submenu {
    position: static;
    box-shadow: none;
    border: 0 none;
    padding-inline-start: ${e("tieredmenu.submenu.mobile.indent")};
    padding-inline-end: 0;
}

.p-tieredmenu-mobile .p-tieredmenu-submenu:dir(rtl) {
    padding-inline-start: 0;
    padding-inline-end: ${e("tieredmenu.submenu.mobile.indent")};
}

.p-tieredmenu-mobile .p-tieredmenu-submenu-icon {
    transition: transform 0.2s;
    transform: rotate(90deg);
}

.p-tieredmenu-mobile .p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    transform: rotate(-90deg);
}
`,xe={submenu:function(t){var n=t.instance,r=t.processedItem;return{display:n.isItemActive(r)?"flex":"none"}}},Me={root:function(t){var n=t.props,r=t.instance;return["p-tieredmenu p-component",{"p-tieredmenu-overlay":n.popup,"p-tieredmenu-mobile":r.queryMatches}]},start:"p-tieredmenu-start",rootList:"p-tieredmenu-root-list",item:function(t){var n=t.instance,r=t.processedItem;return["p-tieredmenu-item",{"p-tieredmenu-item-active":n.isItemActive(r),"p-focus":n.isItemFocused(r),"p-disabled":n.isItemDisabled(r)}]},itemContent:"p-tieredmenu-item-content",itemLink:"p-tieredmenu-item-link",itemIcon:"p-tieredmenu-item-icon",itemLabel:"p-tieredmenu-item-label",submenuIcon:"p-tieredmenu-submenu-icon",submenu:"p-tieredmenu-submenu",separator:"p-tieredmenu-separator",end:"p-tieredmenu-end"},Ce=E.extend({name:"tieredmenu",style:Se,classes:Me,inlineStyles:xe}),Ke={name:"BaseTieredMenu",extends:C,props:{popup:{type:Boolean,default:!1},model:{type:Array,default:null},appendTo:{type:[String,Object],default:"body"},breakpoint:{type:String,default:"960px"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},disabled:{type:Boolean,default:!1},tabindex:{type:Number,default:0},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:Ce,provide:function(){return{$pcTieredMenu:this,$parentInstance:this}}},q={name:"TieredMenuSub",hostName:"TieredMenu",extends:C,emits:["item-click","item-mouseenter","item-mousemove"],container:null,props:{menuId:{type:String,default:null},focusedItemId:{type:String,default:null},items:{type:Array,default:null},visible:{type:Boolean,default:!1},level:{type:Number,default:0},templates:{type:Object,default:null},activeItemPath:{type:Object,default:null},tabindex:{type:Number,default:0}},methods:{getItemId:function(t){return"".concat(this.menuId,"_").concat(t.key)},getItemKey:function(t){return this.getItemId(t)},getItemProp:function(t,n,r){return t&&t.item?R(t.item[n],r):void 0},getItemLabel:function(t){return this.getItemProp(t,"label")},getItemLabelId:function(t){return"".concat(this.menuId,"_").concat(t.key,"_label")},getPTOptions:function(t,n,r){return this.ptm(r,{context:{item:t.item,index:n,active:this.isItemActive(t),focused:this.isItemFocused(t),disabled:this.isItemDisabled(t)}})},isItemActive:function(t){return this.activeItemPath.some(function(n){return n.key===t.key})},isItemVisible:function(t){return this.getItemProp(t,"visible")!==!1},isItemDisabled:function(t){return this.getItemProp(t,"disabled")},isItemFocused:function(t){return this.focusedItemId===this.getItemId(t)},isItemGroup:function(t){return P(t.items)},onEnter:function(){te(this.container,this.level)},onItemClick:function(t,n){this.getItemProp(n,"command",{originalEvent:t,item:n.item}),this.$emit("item-click",{originalEvent:t,processedItem:n,isFocus:!0})},onItemMouseEnter:function(t,n){this.$emit("item-mouseenter",{originalEvent:t,processedItem:n})},onItemMouseMove:function(t,n){this.$emit("item-mousemove",{originalEvent:t,processedItem:n})},getAriaSetSize:function(){var t=this;return this.items.filter(function(n){return t.isItemVisible(n)&&!t.getItemProp(n,"separator")}).length},getAriaPosInset:function(t){var n=this;return t-this.items.slice(0,t).filter(function(r){return n.isItemVisible(r)&&n.getItemProp(r,"separator")}).length+1},getMenuItemProps:function(t,n){return{action:o({class:this.cx("itemLink"),tabindex:-1},this.getPTOptions(t,n,"itemLink")),icon:o({class:[this.cx("itemIcon"),this.getItemProp(t,"icon")]},this.getPTOptions(t,n,"itemIcon")),label:o({class:this.cx("itemLabel")},this.getPTOptions(t,n,"itemLabel")),submenuicon:o({class:this.cx("submenuIcon")},this.getPTOptions(t,n,"submenuIcon"))}},containerRef:function(t){this.container=t}},components:{AngleRightIcon:fe},directives:{ripple:Q}},Ee=["tabindex"],ze=["id","aria-label","aria-disabled","aria-expanded","aria-haspopup","aria-level","aria-setsize","aria-posinset","data-p-active","data-p-focused","data-p-disabled"],Ae=["onClick","onMouseenter","onMousemove"],Te=["href","target"],De=["id"],Be=["id"];function Fe(e,t,n,r,s,i){var l=$("AngleRightIcon"),h=$("TieredMenuSub",!0),d=ne("ripple");return u(),y(N,o({name:"p-tieredmenu",onEnter:i.onEnter},e.ptm("menu.transition")),{default:b(function(){return[n.level===0||n.visible?(u(),m("ul",{key:0,ref:i.containerRef,tabindex:n.tabindex},[(u(!0),m(D,null,ie(n.items,function(a,c){return u(),m(D,{key:i.getItemKey(a)},[i.isItemVisible(a)&&!i.getItemProp(a,"separator")?(u(),m("li",o({key:0,id:i.getItemId(a),style:i.getItemProp(a,"style"),class:[e.cx("item",{processedItem:a}),i.getItemProp(a,"class")],role:"menuitem","aria-label":i.getItemLabel(a),"aria-disabled":i.isItemDisabled(a)||void 0,"aria-expanded":i.isItemGroup(a)?i.isItemActive(a):void 0,"aria-haspopup":i.isItemGroup(a)&&!i.getItemProp(a,"to")?"menu":void 0,"aria-level":n.level+1,"aria-setsize":i.getAriaSetSize(),"aria-posinset":i.getAriaPosInset(c),ref_for:!0},i.getPTOptions(a,c,"item"),{"data-p-active":i.isItemActive(a),"data-p-focused":i.isItemFocused(a),"data-p-disabled":i.isItemDisabled(a)}),[k("div",o({class:e.cx("itemContent"),onClick:function(p){return i.onItemClick(p,a)},onMouseenter:function(p){return i.onItemMouseEnter(p,a)},onMousemove:function(p){return i.onItemMouseMove(p,a)},ref_for:!0},i.getPTOptions(a,c,"itemContent")),[n.templates.item?(u(),y(x(n.templates.item),{key:1,item:a.item,hasSubmenu:i.getItemProp(a,"items"),label:i.getItemLabel(a),props:i.getMenuItemProps(a,c)},null,8,["item","hasSubmenu","label","props"])):re((u(),m("a",o({key:0,href:i.getItemProp(a,"url"),class:e.cx("itemLink"),target:i.getItemProp(a,"target"),tabindex:"-1",ref_for:!0},i.getPTOptions(a,c,"itemLink")),[n.templates.itemicon?(u(),y(x(n.templates.itemicon),{key:0,item:a.item,class:S(e.cx("itemIcon"))},null,8,["item","class"])):i.getItemProp(a,"icon")?(u(),m("span",o({key:1,class:[e.cx("itemIcon"),i.getItemProp(a,"icon")],ref_for:!0},i.getPTOptions(a,c,"itemIcon")),null,16)):g("",!0),k("span",o({id:i.getItemLabelId(a),class:e.cx("itemLabel"),ref_for:!0},i.getPTOptions(a,c,"itemLabel")),B(i.getItemLabel(a)),17,De),i.getItemProp(a,"items")?(u(),m(D,{key:2},[n.templates.submenuicon?(u(),y(x(n.templates.submenuicon),o({key:0,class:e.cx("submenuIcon"),active:i.isItemActive(a),ref_for:!0},i.getPTOptions(a,c,"submenuIcon")),null,16,["class","active"])):(u(),y(l,o({key:1,class:e.cx("submenuIcon"),ref_for:!0},i.getPTOptions(a,c,"submenuIcon")),null,16,["class"]))],64)):g("",!0)],16,Te)),[[d]])],16,Ae),i.isItemVisible(a)&&i.isItemGroup(a)?(u(),y(h,o({key:0,id:i.getItemId(a)+"_list",class:e.cx("submenu"),style:e.sx("submenu",!0,{processedItem:a}),"aria-labelledby":i.getItemLabelId(a),role:"menu",menuId:n.menuId,focusedItemId:n.focusedItemId,items:a.items,templates:n.templates,activeItemPath:n.activeItemPath,level:n.level+1,visible:i.isItemActive(a)&&i.isItemGroup(a),pt:e.pt,unstyled:e.unstyled,onItemClick:t[0]||(t[0]=function(f){return e.$emit("item-click",f)}),onItemMouseenter:t[1]||(t[1]=function(f){return e.$emit("item-mouseenter",f)}),onItemMousemove:t[2]||(t[2]=function(f){return e.$emit("item-mousemove",f)}),ref_for:!0},e.ptm("submenu")),null,16,["id","class","style","aria-labelledby","menuId","focusedItemId","items","templates","activeItemPath","level","visible","pt","unstyled"])):g("",!0)],16,ze)):g("",!0),i.isItemVisible(a)&&i.getItemProp(a,"separator")?(u(),m("li",o({key:1,id:i.getItemId(a),style:i.getItemProp(a,"style"),class:[e.cx("separator"),i.getItemProp(a,"class")],role:"separator",ref_for:!0},e.ptm("separator")),null,16,Be)):g("",!0)],64)}),128))],8,Ee)):g("",!0)]}),_:1},16,["onEnter"])}q.render=Fe;var H={name:"TieredMenu",extends:Ke,inheritAttrs:!1,emits:["focus","blur","before-show","before-hide","hide","show"],outsideClickListener:null,matchMediaListener:null,scrollHandler:null,resizeListener:null,target:null,container:null,menubar:null,searchTimeout:null,searchValue:null,data:function(){return{focused:!1,focusedItemInfo:{index:-1,level:0,parentKey:""},activeItemPath:[],visible:!this.popup,submenuVisible:!1,dirty:!1,query:null,queryMatches:!1}},watch:{activeItemPath:function(t){this.popup||(P(t)?(this.bindOutsideClickListener(),this.bindResizeListener()):(this.unbindOutsideClickListener(),this.unbindResizeListener()))}},mounted:function(){this.bindMatchMediaListener()},beforeUnmount:function(){this.unbindOutsideClickListener(),this.unbindResizeListener(),this.unbindMatchMediaListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.container&&this.autoZIndex&&T.clear(this.container),this.target=null,this.container=null},methods:{getItemProp:function(t,n){return t?R(t[n]):void 0},getItemLabel:function(t){return this.getItemProp(t,"label")},isItemDisabled:function(t){return this.getItemProp(t,"disabled")},isItemVisible:function(t){return this.getItemProp(t,"visible")!==!1},isItemGroup:function(t){return P(this.getItemProp(t,"items"))},isItemSeparator:function(t){return this.getItemProp(t,"separator")},getProccessedItemLabel:function(t){return t?this.getItemLabel(t.item):void 0},isProccessedItemGroup:function(t){return t&&P(t.items)},toggle:function(t){this.visible?this.hide(t,!0):this.show(t)},show:function(t,n){this.popup&&(this.$emit("before-show"),this.visible=!0,this.target=this.target||t.currentTarget,this.relatedTarget=t.relatedTarget||null),n&&L(this.menubar)},hide:function(t,n){this.popup&&(this.$emit("before-hide"),this.visible=!1),this.activeItemPath=[],this.focusedItemInfo={index:-1,level:0,parentKey:""},n&&L(this.relatedTarget||this.target||this.menubar),this.dirty=!1},onFocus:function(t){this.focused=!0,this.popup||(this.focusedItemInfo=this.focusedItemInfo.index!==-1?this.focusedItemInfo:{index:this.findFirstFocusedItemIndex(),level:0,parentKey:""}),this.$emit("focus",t)},onBlur:function(t){this.focused=!1,this.focusedItemInfo={index:-1,level:0,parentKey:""},this.searchValue="",this.dirty=!1,this.$emit("blur",t)},onKeyDown:function(t){if(this.disabled){t.preventDefault();return}var n=t.metaKey||t.ctrlKey;switch(t.code){case"ArrowDown":this.onArrowDownKey(t);break;case"ArrowUp":this.onArrowUpKey(t);break;case"ArrowLeft":this.onArrowLeftKey(t);break;case"ArrowRight":this.onArrowRightKey(t);break;case"Home":this.onHomeKey(t);break;case"End":this.onEndKey(t);break;case"Space":this.onSpaceKey(t);break;case"Enter":case"NumpadEnter":this.onEnterKey(t);break;case"Escape":this.onEscapeKey(t);break;case"Tab":this.onTabKey(t);break;case"PageDown":case"PageUp":case"Backspace":case"ShiftLeft":case"ShiftRight":break;default:!n&&ee(t.key)&&this.searchItems(t,t.key);break}},onItemChange:function(t,n){var r=t.processedItem,s=t.isFocus;if(!K(r)){var i=r.index,l=r.key,h=r.level,d=r.parentKey,a=r.items,c=P(a),f=this.activeItemPath.filter(function(p){return p.parentKey!==d&&p.parentKey!==l});c&&(f.push(r),this.submenuVisible=!0),this.focusedItemInfo={index:i,level:h,parentKey:d},c&&(this.dirty=!0),s&&L(this.menubar),!(n==="hover"&&this.queryMatches)&&(this.activeItemPath=f)}},onOverlayClick:function(t){ue.emit("overlay-click",{originalEvent:t,target:this.target})},onItemClick:function(t){var n=t.originalEvent,r=t.processedItem,s=this.isProccessedItemGroup(r),i=K(r.parent),l=this.isSelected(r);if(l){var h=r.index,d=r.key,a=r.level,c=r.parentKey;this.activeItemPath=this.activeItemPath.filter(function(p){return d!==p.key&&d.startsWith(p.key)}),this.focusedItemInfo={index:h,level:a,parentKey:c},this.dirty=!i,L(this.menubar)}else if(s)this.onItemChange(t);else{var f=i?r:this.activeItemPath.find(function(p){return p.parentKey===""});this.hide(n),this.changeFocusedItemIndex(n,f?f.index:-1),L(this.menubar)}},onItemMouseEnter:function(t){this.dirty&&this.onItemChange(t,"hover")},onItemMouseMove:function(t){this.focused&&this.changeFocusedItemIndex(t,t.processedItem.index)},onArrowDownKey:function(t){var n=this.focusedItemInfo.index!==-1?this.findNextItemIndex(this.focusedItemInfo.index):this.findFirstFocusedItemIndex();this.changeFocusedItemIndex(t,n),t.preventDefault()},onArrowUpKey:function(t){if(t.altKey){if(this.focusedItemInfo.index!==-1){var n=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(n);!r&&this.onItemChange({originalEvent:t,processedItem:n})}this.popup&&this.hide(t,!0),t.preventDefault()}else{var s=this.focusedItemInfo.index!==-1?this.findPrevItemIndex(this.focusedItemInfo.index):this.findLastFocusedItemIndex();this.changeFocusedItemIndex(t,s),t.preventDefault()}},onArrowLeftKey:function(t){var n=this,r=this.visibleItems[this.focusedItemInfo.index],s=this.activeItemPath.find(function(l){return l.key===r.parentKey}),i=K(r.parent);i||(this.focusedItemInfo={index:-1,parentKey:s?s.parentKey:""},this.searchValue="",this.onArrowDownKey(t)),this.activeItemPath=this.activeItemPath.filter(function(l){return l.parentKey!==n.focusedItemInfo.parentKey}),t.preventDefault()},onArrowRightKey:function(t){var n=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(n);r&&(this.onItemChange({originalEvent:t,processedItem:n}),this.focusedItemInfo={index:-1,parentKey:n.key},this.searchValue="",this.onArrowDownKey(t)),t.preventDefault()},onHomeKey:function(t){this.changeFocusedItemIndex(t,this.findFirstItemIndex()),t.preventDefault()},onEndKey:function(t){this.changeFocusedItemIndex(t,this.findLastItemIndex()),t.preventDefault()},onEnterKey:function(t){if(this.focusedItemInfo.index!==-1){var n=z(this.menubar,'li[id="'.concat("".concat(this.focusedItemId),'"]')),r=n&&z(n,'[data-pc-section="itemlink"]');if(r?r.click():n&&n.click(),!this.popup){var s=this.visibleItems[this.focusedItemInfo.index],i=this.isProccessedItemGroup(s);!i&&(this.focusedItemInfo.index=this.findFirstFocusedItemIndex())}}t.preventDefault()},onSpaceKey:function(t){this.onEnterKey(t)},onEscapeKey:function(t){if(this.popup||this.focusedItemInfo.level!==0){var n=this.focusedItemInfo;this.hide(t,!1),this.focusedItemInfo={index:Number(n.parentKey.split("_")[0]),level:0,parentKey:""},this.popup&&L(this.target)}t.preventDefault()},onTabKey:function(t){if(this.focusedItemInfo.index!==-1){var n=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(n);!r&&this.onItemChange({originalEvent:t,processedItem:n})}this.hide()},onEnter:function(t){this.autoZIndex&&T.set("menu",t,this.baseZIndex+this.$primevue.config.zIndex.menu),_(t,{position:"absolute",top:"0"}),this.alignOverlay(),L(this.menubar),this.scrollInView()},onAfterEnter:function(){this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.$emit("show")},onLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.$emit("hide"),this.container=null,this.dirty=!1},onAfterLeave:function(t){this.autoZIndex&&T.clear(t)},alignOverlay:function(){Y(this.container,this.target);var t=A(this.target);t>A(this.container)&&(this.container.style.minWidth=A(this.target)+"px")},bindOutsideClickListener:function(){var t=this;this.outsideClickListener||(this.outsideClickListener=function(n){var r=t.container&&!t.container.contains(n.target),s=t.popup?!(t.target&&(t.target===n.target||t.target.contains(n.target))):!0;r&&s&&t.hide()},document.addEventListener("click",this.outsideClickListener,!0))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener,!0),this.outsideClickListener=null)},bindScrollListener:function(){var t=this;this.scrollHandler||(this.scrollHandler=new X(this.target,function(n){t.hide(n,!0)})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var t=this;this.resizeListener||(this.resizeListener=function(n){J()||t.hide(n,!0)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},bindMatchMediaListener:function(){var t=this;if(!this.matchMediaListener){var n=matchMedia("(max-width: ".concat(this.breakpoint,")"));this.query=n,this.queryMatches=n.matches,this.matchMediaListener=function(){t.queryMatches=n.matches},this.query.addEventListener("change",this.matchMediaListener)}},unbindMatchMediaListener:function(){this.matchMediaListener&&(this.query.removeEventListener("change",this.matchMediaListener),this.matchMediaListener=null)},isItemMatched:function(t){var n;return this.isValidItem(t)&&((n=this.getProccessedItemLabel(t))===null||n===void 0?void 0:n.toLocaleLowerCase().startsWith(this.searchValue.toLocaleLowerCase()))},isValidItem:function(t){return!!t&&!this.isItemDisabled(t.item)&&!this.isItemSeparator(t.item)&&this.isItemVisible(t.item)},isValidSelectedItem:function(t){return this.isValidItem(t)&&this.isSelected(t)},isSelected:function(t){return this.activeItemPath.some(function(n){return n.key===t.key})},findFirstItemIndex:function(){var t=this;return this.visibleItems.findIndex(function(n){return t.isValidItem(n)})},findLastItemIndex:function(){var t=this;return F(this.visibleItems,function(n){return t.isValidItem(n)})},findNextItemIndex:function(t){var n=this,r=t<this.visibleItems.length-1?this.visibleItems.slice(t+1).findIndex(function(s){return n.isValidItem(s)}):-1;return r>-1?r+t+1:t},findPrevItemIndex:function(t){var n=this,r=t>0?F(this.visibleItems.slice(0,t),function(s){return n.isValidItem(s)}):-1;return r>-1?r:t},findSelectedItemIndex:function(){var t=this;return this.visibleItems.findIndex(function(n){return t.isValidSelectedItem(n)})},findFirstFocusedItemIndex:function(){var t=this.findSelectedItemIndex();return t<0?this.findFirstItemIndex():t},findLastFocusedItemIndex:function(){var t=this.findSelectedItemIndex();return t<0?this.findLastItemIndex():t},searchItems:function(t,n){var r=this;this.searchValue=(this.searchValue||"")+n;var s=-1,i=!1;return this.focusedItemInfo.index!==-1?(s=this.visibleItems.slice(this.focusedItemInfo.index).findIndex(function(l){return r.isItemMatched(l)}),s=s===-1?this.visibleItems.slice(0,this.focusedItemInfo.index).findIndex(function(l){return r.isItemMatched(l)}):s+this.focusedItemInfo.index):s=this.visibleItems.findIndex(function(l){return r.isItemMatched(l)}),s!==-1&&(i=!0),s===-1&&this.focusedItemInfo.index===-1&&(s=this.findFirstFocusedItemIndex()),s!==-1&&this.changeFocusedItemIndex(t,s),this.searchTimeout&&clearTimeout(this.searchTimeout),this.searchTimeout=setTimeout(function(){r.searchValue="",r.searchTimeout=null},500),i},changeFocusedItemIndex:function(t,n){this.focusedItemInfo.index!==n&&(this.focusedItemInfo.index=n,this.scrollInView())},scrollInView:function(){var t=arguments.length>0&&arguments[0]!==void 0?arguments[0]:-1,n=t!==-1?"".concat(this.$id,"_").concat(t):this.focusedItemId,r=z(this.menubar,'li[id="'.concat(n,'"]'));r&&r.scrollIntoView&&r.scrollIntoView({block:"nearest",inline:"start"})},createProcessedItems:function(t){var n=this,r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:0,s=arguments.length>2&&arguments[2]!==void 0?arguments[2]:{},i=arguments.length>3&&arguments[3]!==void 0?arguments[3]:"",l=[];return t&&t.forEach(function(h,d){var a=(i!==""?i+"_":"")+d,c={item:h,index:d,level:r,key:a,parent:s,parentKey:i};c.items=n.createProcessedItems(h.items,r+1,c,a),l.push(c)}),l},containerRef:function(t){this.container=t},menubarRef:function(t){this.menubar=t?t.$el:void 0}},computed:{processedItems:function(){return this.createProcessedItems(this.model||[])},visibleItems:function(){var t=this,n=this.activeItemPath.find(function(r){return r.key===t.focusedItemInfo.parentKey});return n?n.items:this.processedItems},focusedItemId:function(){return this.focusedItemInfo.index!==-1?"".concat(this.$id).concat(P(this.focusedItemInfo.parentKey)?"_"+this.focusedItemInfo.parentKey:"","_").concat(this.focusedItemInfo.index):null}},components:{TieredMenuSub:q,Portal:W}},Ve=["id"];function Oe(e,t,n,r,s,i){var l=$("TieredMenuSub"),h=$("Portal");return u(),y(h,{appendTo:e.appendTo,disabled:!e.popup},{default:b(function(){return[I(N,o({name:"p-connected-overlay",onEnter:i.onEnter,onAfterEnter:i.onAfterEnter,onLeave:i.onLeave,onAfterLeave:i.onAfterLeave},e.ptm("transition")),{default:b(function(){return[s.visible?(u(),m("div",o({key:0,ref:i.containerRef,id:e.$id,class:e.cx("root"),onClick:t[0]||(t[0]=function(){return i.onOverlayClick&&i.onOverlayClick.apply(i,arguments)})},e.ptmi("root")),[e.$slots.start?(u(),m("div",o({key:0,class:e.cx("start")},e.ptm("start")),[v(e.$slots,"start")],16)):g("",!0),I(l,o({ref:i.menubarRef,id:e.$id+"_list",class:e.cx("rootList"),tabindex:e.disabled?-1:e.tabindex,role:"menubar","aria-label":e.ariaLabel,"aria-labelledby":e.ariaLabelledby,"aria-disabled":e.disabled||void 0,"aria-orientation":"vertical","aria-activedescendant":s.focused?i.focusedItemId:void 0,menuId:e.$id,focusedItemId:s.focused?i.focusedItemId:void 0,items:i.processedItems,templates:e.$slots,activeItemPath:s.activeItemPath,level:0,visible:s.submenuVisible,pt:e.pt,unstyled:e.unstyled,onFocus:i.onFocus,onBlur:i.onBlur,onKeydown:i.onKeyDown,onItemClick:i.onItemClick,onItemMouseenter:i.onItemMouseEnter,onItemMousemove:i.onItemMouseMove},e.ptm("rootList")),null,16,["id","class","tabindex","aria-label","aria-labelledby","aria-disabled","aria-activedescendant","menuId","focusedItemId","items","templates","activeItemPath","visible","pt","unstyled","onFocus","onBlur","onKeydown","onItemClick","onItemMouseenter","onItemMousemove"]),e.$slots.end?(u(),m("div",o({key:1,class:e.cx("end")},e.ptm("end")),[v(e.$slots,"end")],16)):g("",!0)],16,Ve)):g("",!0)]}),_:3},16,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo","disabled"])}H.render=Oe;var Re=({dt:e})=>`
.p-splitbutton {
    display: inline-flex;
    position: relative;
    border-radius: ${e("splitbutton.border.radius")};
}

.p-splitbutton-button {
    border-start-end-radius: 0;
    border-end-end-radius: 0;
    border-inline-end: 0 none;
}

.p-splitbutton-button:focus-visible,
.p-splitbutton-dropdown:focus-visible {
    z-index: 1;
}

.p-splitbutton-button:not(:disabled):hover,
.p-splitbutton-button:not(:disabled):active {
    border-inline-end: 0 none;
}

.p-splitbutton-dropdown {
    border-start-start-radius: 0;
    border-end-start-radius: 0;
}

.p-splitbutton .p-menu {
    min-width: 100%;
}

.p-splitbutton-fluid {
    display: flex;
}

.p-splitbutton-rounded .p-splitbutton-dropdown {
    border-start-end-radius: ${e("splitbutton.rounded.border.radius")};
    border-end-end-radius: ${e("splitbutton.rounded.border.radius")};
}

.p-splitbutton-rounded .p-splitbutton-button {
    border-start-start-radius: ${e("splitbutton.rounded.border.radius")};
    border-end-start-radius: ${e("splitbutton.rounded.border.radius")};
}

.p-splitbutton-raised {
    box-shadow: ${e("splitbutton.raised.shadow")};
}
`,Ne={root:function(t){var n=t.instance,r=t.props;return["p-splitbutton p-component",{"p-splitbutton-raised":r.raised,"p-splitbutton-rounded":r.rounded,"p-splitbutton-fluid":n.hasFluid}]},pcButton:"p-splitbutton-button",pcDropdown:"p-splitbutton-dropdown"},Ge=E.extend({name:"splitbutton",style:Re,classes:Ne}),qe={name:"BaseSplitButton",extends:C,props:{label:{type:String,default:null},icon:{type:String,default:null},model:{type:Array,default:null},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},appendTo:{type:[String,Object],default:"body"},disabled:{type:Boolean,default:!1},fluid:{type:Boolean,default:null},class:{type:null,default:null},style:{type:null,default:null},buttonProps:{type:null,default:null},menuButtonProps:{type:null,default:null},menuButtonIcon:{type:String,default:void 0},dropdownIcon:{type:String,default:void 0},severity:{type:String,default:null},raised:{type:Boolean,default:!1},rounded:{type:Boolean,default:!1},text:{type:Boolean,default:!1},outlined:{type:Boolean,default:!1},size:{type:String,default:null},plain:{type:Boolean,default:!1}},style:Ge,provide:function(){return{$pcSplitButton:this,$parentInstance:this}}},Z={name:"SplitButton",extends:qe,inheritAttrs:!1,emits:["click"],inject:{$pcFluid:{default:null}},data:function(){return{isExpanded:!1}},mounted:function(){var t=this;this.$watch("$refs.menu.visible",function(n){t.isExpanded=n})},methods:{onDropdownButtonClick:function(t){t&&t.preventDefault(),this.$refs.menu.toggle({currentTarget:this.$el,relatedTarget:this.$refs.button.$el}),this.isExpanded=this.$refs.menu.visible},onDropdownKeydown:function(t){(t.code==="ArrowDown"||t.code==="ArrowUp")&&(this.onDropdownButtonClick(),t.preventDefault())},onDefaultButtonClick:function(t){this.isExpanded&&this.$refs.menu.hide(t),this.$emit("click",t)}},computed:{containerClass:function(){return[this.cx("root"),this.class]},hasFluid:function(){return K(this.fluid)?!!this.$pcFluid:this.fluid}},components:{PVSButton:ae,PVSMenu:H,ChevronDownIcon:me}},He=["data-p-severity"];function Ze(e,t,n,r,s,i){var l=$("PVSButton"),h=$("PVSMenu");return u(),m("div",o({class:i.containerClass,style:e.style},e.ptmi("root"),{"data-p-severity":e.severity}),[I(l,o({type:"button",class:e.cx("pcButton"),label:e.label,disabled:e.disabled,severity:e.severity,text:e.text,icon:e.icon,outlined:e.outlined,size:e.size,fluid:e.fluid,"aria-label":e.label,onClick:i.onDefaultButtonClick},e.buttonProps,{pt:e.ptm("pcButton"),unstyled:e.unstyled}),V({default:b(function(){return[v(e.$slots,"default")]}),_:2},[e.$slots.icon?{name:"icon",fn:b(function(d){return[v(e.$slots,"icon",{class:S(d.class)},function(){return[k("span",o({class:[e.icon,d.class]},e.ptm("pcButton").icon,{"data-pc-section":"buttonicon"}),null,16)]})]}),key:"0"}:void 0]),1040,["class","label","disabled","severity","text","icon","outlined","size","fluid","aria-label","onClick","pt","unstyled"]),I(l,o({ref:"button",type:"button",class:e.cx("pcDropdown"),disabled:e.disabled,"aria-haspopup":"true","aria-expanded":s.isExpanded,"aria-controls":e.$id+"_overlay",onClick:i.onDropdownButtonClick,onKeydown:i.onDropdownKeydown,severity:e.severity,text:e.text,outlined:e.outlined,size:e.size,unstyled:e.unstyled},e.menuButtonProps,{pt:e.ptm("pcDropdown")}),{icon:b(function(d){return[v(e.$slots,e.$slots.dropdownicon?"dropdownicon":"menubuttonicon",{class:S(d.class)},function(){return[(u(),y(x(e.menuButtonIcon||e.dropdownIcon?"span":"ChevronDownIcon"),o({class:[e.dropdownIcon||e.menuButtonIcon,d.class]},e.ptm("pcDropdown").icon,{"data-pc-section":"menubuttonicon"}),null,16,["class"]))]})]}),_:3},16,["class","disabled","aria-expanded","aria-controls","onClick","onKeydown","severity","text","outlined","size","unstyled","pt"]),I(h,{ref:"menu",id:e.$id+"_overlay",model:e.model,popup:!0,autoZIndex:e.autoZIndex,baseZIndex:e.baseZIndex,appendTo:e.appendTo,unstyled:e.unstyled,pt:e.ptm("pcMenu")},V({_:2},[e.$slots.menuitemicon?{name:"itemicon",fn:b(function(d){return[v(e.$slots,"menuitemicon",{item:d.item,class:S(d.class)})]}),key:"0"}:void 0,e.$slots.item?{name:"item",fn:b(function(d){return[v(e.$slots,"item",{item:d.item,hasSubmenu:d.hasSubmenu,label:d.label,props:d.props})]}),key:"1"}:void 0]),1032,["id","model","autoZIndex","baseZIndex","appendTo","unstyled","pt"])],16,He)}Z.render=Ze;var je=({dt:e})=>`
.p-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    padding: ${e("toolbar.padding")};
    background: ${e("toolbar.background")};
    border: 1px solid ${e("toolbar.border.color")};
    color: ${e("toolbar.color")};
    border-radius: ${e("toolbar.border.radius")};
    gap: ${e("toolbar.gap")};
}

.p-toolbar-start,
.p-toolbar-center,
.p-toolbar-end {
    display: flex;
    align-items: center;
}
`,Ue={root:"p-toolbar p-component",start:"p-toolbar-start",center:"p-toolbar-center",end:"p-toolbar-end"},We=E.extend({name:"toolbar",style:je,classes:Ue}),Qe={name:"BaseToolbar",extends:C,props:{ariaLabelledby:{type:String,default:null}},style:We,provide:function(){return{$pcToolbar:this,$parentInstance:this}}},j={name:"Toolbar",extends:Qe,inheritAttrs:!1},Je=["aria-labelledby"];function Xe(e,t,n,r,s,i){return u(),m("div",o({class:e.cx("root"),role:"toolbar","aria-labelledby":e.ariaLabelledby},e.ptmi("root")),[k("div",o({class:e.cx("start")},e.ptm("start")),[v(e.$slots,"start")],16),k("div",o({class:e.cx("center")},e.ptm("center")),[v(e.$slots,"center")],16),k("div",o({class:e.cx("end")},e.ptm("end")),[v(e.$slots,"end")],16)],16,Je)}j.render=Xe;const Ye={class:"card"},_e={class:"flex items-center gap-8"},et={class:"text-xl font-bold m-0"},tt="Manager",nt="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png",it={__name:"NavbarLayout",setup(e){const t=se(),n=[{label:"Accounts",icon:"pi pi-user"},{label:"Logout",icon:"pi pi-sign-out"}],r=oe(()=>{const s=t.url,i={"/":"Dashboard","/quotations":"Quotations","/quotations/create":"Create Quotation","/agreements":"Agreements","/agreements/create":"Create Agreement","/invoices":"Invoices","/invoices/create":"Create Invoice","/settings":"Settings","/settings/customers":"Customers","/settings/products":"Items","/settings/product-categories":"Product Categories","/settings/customer-categories":"Customer Categories"};return s.includes("/agreements/")&&s.includes("/edit")?(s.split("/")[2],"Edit Agreement"):s.includes("/quotations/")&&s.includes("/update")?`Edit Quotation - ${s.split("/")[2]}`:i[s]||"Edit Quotation"});return(s,i)=>(u(),m("div",Ye,[I(w(j),{class:"p-4"},{start:b(()=>[k("div",_e,[k("h1",et,B(r.value),1),I(w(le),null,{default:b(()=>[I(w(de),null,{default:b(()=>i[0]||(i[0]=[k("i",{class:"pi pi-search"},null,-1)])),_:1}),I(w(ce),{size:"small",class:"w-80",placeholder:"Find invoices, clients, and more"})]),_:1})])]),end:b(()=>[I(w(Z),{label:tt,model:n,class:"mr-3",size:"small",outlined:""}),I(w(G),{image:nt,style:{width:"32px",height:"32px"},size:"large",shape:"circle"})]),_:1})]))}},lt=pe(it,[["__scopeId","data-v-b6d9654a"]]);export{lt as N};
