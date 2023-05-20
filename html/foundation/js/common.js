/**
* 共通Javascriptクラス
*/ 
/**
* WebApiクラス
*/
export const WebApi = class{
    #contentType = null;
    #responseType = null;
    #headers = null;
    #cache = null
    constructor({contentType,responseType,headers,cache}) {
      this.#contentType = typeof contentType === "undefined" ? null : contentType;
      this.#responseType = typeof responseType === "undefined" ? null : responseType;
      this.#headers = typeof headers === "undefined" ? null : headers;
      this.#cache = typeof cache === "undefined" ? null : cache;
    }
    call(url,method,data = null,jsonencode = false){
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        if(this.#contentType !== null){
          xhr.setRequestHeader("Content-Type",this.#contentType);
        }
        if(this.#responseType !== null){
          xhr.responseType = this.#responseType;
        }

        if(this.#cache === null || this.#cache === false){
          xhr.setRequestHeader('Pragma', 'no-cache');
          xhr.setRequestHeader('Cache-Control', 'no-cache');
          xhr.setRequestHeader('If-Modified-Since', 'Thu, 01 Jun 1970 00:00:00 GMT');
        }

        xhr.onload = function(e){
          if(this.status == 200){
            // テキストも含め包括するのでresponseを返す
            resolve(this.response);
          }else{
            reject(new Error(this.statusText));
          }
        }

        if(jsonencode){
          data = JSON.stringify(data);
        }
        // パラメータがあるなしで分岐
        if(data != null){
          xhr.send(data);
        }else{
          xhr.send();
        }
      });
    }
};

/**
* Modalクラス
*/
export const Modal = class{
  #body = "";
  /**
  * text:右ボタン名
  */
  #exec = {};
  /**
  * text:左ボタン名
  * reloadByClose:左ボタンが押された時リロードをするかどうか
  */
  #close = {};

  #modalElem = null;
  #bgElem = null;
  #wrapperElem = null;
  #contentsElem = null;
  #buttonsElem = null;
  #closeElem = null;
  #execElem = null;

  #execFunc = () => false;

  constructor({body,exec,close}) {
    if(body && typeof(body) === 'string'){
      this.#body = body;
    }

    if(exec){
      this.#exec = exec;
    }

    if(close){
      this.#close = close;
    }
  }

  fadeIn()
  {
    // 左ボタンについて
    let leftButtonText = '閉じる';
    if(Object.keys(this.#close).length !== 0){
      if(this.#close.hasOwnProperty('text')){
        leftButtonText = this.#close.text;
      }
    }
    this.#closeElem = document.createElement('button');
    this.#closeElem.className = '__modal__button __modal__close__btn';
    this.#closeElem.textContent = leftButtonText;

    // モーダル外エリア
    this.#bgElem = document.createElement('div');
    this.#bgElem.className = '__modal__bg';

    // ボタン配置エリア
    this.#buttonsElem = document.createElement('div');
    this.#buttonsElem.className = '__modal__buttons';
    this.#buttonsElem.appendChild(this.#closeElem);

    // 右ボタンについて
    if(Object.keys(this.#exec).length !== 0){
      this.#execElem = document.createElement('button');
      this.#execElem.className = '__modal__button __modal__exec__btn';
      let rightButtonText = "実行";
      if(this.#exec.hasOwnProperty('text')){
        rightButtonText = this.#exec.text;
      }
      this.#execElem.textContent = rightButtonText;
      this.#buttonsElem.appendChild(this.#execElem);
    }else{
      this.#buttonsElem.classList.add('__modal__center');
    }

    // コンテンツエリア
    this.#contentsElem = document.createElement('div');
    this.#contentsElem.className = '__modal__contents';
    this.#contentsElem.insertAdjacentHTML('afterbegin',this.#body);
    this.#contentsElem.appendChild(this.#buttonsElem);
    // コンテンツの包むエリア
    this.#wrapperElem = document.createElement('div');
    this.#wrapperElem.className = '__modal__wrapper';
    this.#wrapperElem.appendChild(this.#contentsElem);
    // モーダルアウトライン
    this.#modalElem = document.createElement('section');
    this.#modalElem.className = '__modal__area __fade__in';
    this.#modalElem.appendChild(this.#bgElem);
    this.#modalElem.appendChild(this.#wrapperElem);
    // ボディに詰め込む
    let tagbody = document.getElementsByTagName('body');
    tagbody[0].insertBefore(this.#modalElem,tagbody[0].children[0]);

    this.#closeElem.onclick = () => {
      this.fadeOut();
    }

    this.#bgElem.onclick = () => {
      this.fadeOut();
    }
    if(this.#execElem){
      this.#execElem.onclick = () => {
        this.execute();
      }
    }
  }

  /**
  * モーダル非表示（remove）
  */
  fadeOut(){
    const rmAction = () => false;
    this.#closeElem.onclick = rmAction;
    this.#bgElem.onclick = rmAction;
    this.#modalElem.remove();
    if(this.#close.reloadByClose === true){
      location.reload();
    }
  }
  /**
  * executeボタン
  */
  execute(){
    this.execFunc(this);
  }

  setExecFunc(fn){
    if(typeof(fn) !== 'function'){
      console.log('fn is type of error');
      exit();
    }
    this.execFunc = fn;
  }
};

export const LoadingCircle = class{
  #message = "";
  #id = "loading";

  constructor({id,message}) {
    if(id || typeof(id) === 'string'){
      this.#id = id;
    }

    if(message != null || typeof(message) === 'string'){
      this.#message = message;
    }
  }
  /**
  * ローディングサークル表示
  */
  show(){
    // ローディングサークルメッセージ部分作成
    let msgtag = document.createElement('div');
    msgtag.className = 'loadingMsg';
    msgtag.textContent = this.#message;
    if(document.getElementById('loading') == null){
      // ローディングサークル本体作成
      let loadingtag = document.createElement('div');
      loadingtag.setAttribute('id', 'loading');
      loadingtag.appendChild(msgtag);
      // bodyに追加
      let tagbody = document.getElementsByTagName('body');
      tagbody[0].insertBefore(loadingtag,tagbody[0].children[0]);
    }
  }

  /**
  * ローディングサークル非表示
  */
  hide(){
    document.getElementById('loading').remove();
  }
};
/**
* ユーティリティ
*/
export const Utils = class{
  static baseName(str){
    let base = new String(str).substring(str.lastIndexOf('/') + 1);
    if(base.lastIndexOf(".") != -1)
    base = base.substring(0, base.lastIndexOf("."));
    return base;
  }

  static getPartOfPath(no){
    return location.pathname.split('/')[no];
  }

  static openTab(url){
    const tab = window.open(url);
    window.focus();

    return tab;
  }

  static closeTab(tab){
    if( (tab) && (!tab.closed) ){
      tab.close();
    }
    tab = null;
  }
};

