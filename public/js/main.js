const showMenu = (toggleId, navId) =>{
    const toggle = document.getElementById(toggleId),
        nav = document.getElementById(navId);

    if (toggle && nav) {
        toggle.addEventListener('click', ()=>{
            nav.classList.toggle('show');
        } );
    }
}

showMenu('nav-toggle', 'nav-menu');

gsap.from('.header__title', {opacity: -1, duration: 1.5, delay: 1})
gsap.from('.header__description', {opacity: 0, duration: 1, delay: 1, x: 100})
gsap.from('.header__button', {opacity: 0, duration: 1.2, delay: 1.2, y: 30})



$(document).ready(function()
{
    "use strict";

    var menuActive = false;

    initCustomDropdown();


    function initCustomDropdown()
    {
        if($('.custom_dropdown_placeholder').length && $('.custom_list').length)
        {
            var placeholder = $('.custom_dropdown_placeholder');
            var list = $('.custom_list');
        }

        placeholder.on('click', function (ev)
        {
            if(list.hasClass('active'))
            {
                list.removeClass('active');
            }
            else
            {
                list.addClass('active');
            }

            $(document).one('click', function closeForm(e)
            {
                if($(e.target).hasClass('clc'))
                {
                    $(document).one('click', closeForm);
                }
                else
                {
                    list.removeClass('active');
                }
            });

        });

        $('.custom_list a').on('click', function (ev)
        {
            ev.preventDefault();
            var index = $(this).parent().index();

            placeholder.text( $(this).text() ).css('opacity', '1');

            if(list.hasClass('active'))
            {
                list.removeClass('active');
            }
            else
            {
                list.addClass('active');
            }
        });


        $('select').on('change', function (e)
        {
            placeholder.text(this.value);

            $(this).animate({width: placeholder.width() + 'px' });
        });
    }
});


