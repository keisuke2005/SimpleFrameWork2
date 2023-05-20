import {LoadingCircle} from "./common.js";

export const Page = class{
    #pageStack = [];
    #loadingCircle;
    constructor(){
        this.loadingCircle = new LoadingCircle({});
    }
    pageActive(pageName,param = {}){
        pagePush(pageName);
        document.querySelector(pageName).classList.add('active');
        let startUpFunc = eval(pageName.replace(/#/g,'') + ".startup");
        startUpFunc();
    }

    pagePush(pageName){
        base.pageStack.push(pageName);
    }

    pagePop(pageName){
        return base.pageStack.pop();
    }

    startUp(){
        this.loadingCircle.show();
        const initPageId = this.getInitPageId();
        this.page();
        this.pageActive(initPageId);
        this.loadingCircle.hide();
    }

    page(){
        
    }
    getInitPageId(){
        return "";
    }
    
};