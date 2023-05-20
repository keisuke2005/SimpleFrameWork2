$(function() {

    let scroll = 0;

    /*  メインビジュアルの切り替え */
    $('.img-wrap img:nth-child(n+2)').hide();
    setInterval(function() {
      $(".img-wrap img:first-child").fadeOut(2000);
      $(".img-wrap img:nth-child(2)").fadeIn(2000);
      $(".img-wrap img:first-child").appendTo(".img-wrap");
      }, 4000);

    /* フェードイン */
    /* メインビジュアル */
    $('.main-visual p').addClass('fadein-active01');
    $('header').addClass('fadein-active02');

    $(window).scroll(function () {
      $('.fadein-scroll').each(function(){
        // .fadein-scrollクラスの要素位置
        let elemPos = $(this).offset().top;
        // 画面の高さ
        let windowHeight = $(window).height();
        // scroll位置
        let scroll = $(window).scrollTop();

        // .dadein-scrollクラスの要素が視界に少しでも入ればクラス付与
        if (scroll > elemPos - windowHeight ) {
          $(this).addClass('fadein-active01');
        }
        // scroll位置が要素位置+要素の高さになるか、クラス付与する位置に戻るか
        if (scroll > elemPos + $(this).height() || scroll < elemPos - windowHeight) {
          $(this).removeClass('fadein-active01');
        }
      });
    });

        /* ハンバーガーメニュー */
    $('.hamburger').on('click',function() {
        $('.nav-02').toggleClass('nav-02-active');
        $(this).toggleClass('close');
    });
  });