jQuery(document).ready(function ($) {

    $('[name="synpro_hr_options[hide_mode_all]"]').on('change', function () {
        if ($(this).is(":checked"))
        {
            jQuery('[name="synpro_hr_options[hide_mode_post_page]"]').attr('checked', 'checked');
            jQuery('[name="synpro_hr_options[hide_mode_comments]"]').attr('checked', 'checked');
            jQuery('[name="synpro_hr_options[hide_mode_all_comments_admin]"]').attr('checked', 'checked');
        } else {
            jQuery('[name="synpro_hr_options[hide_mode_post_page]"]').removeAttr('checked');
            jQuery('[name="synpro_hr_options[hide_mode_comments]"]').removeAttr('checked');
            jQuery('[name="synpro_hr_options[hide_mode_all_comments_admin]"]').removeAttr('checked');
        }
    });
    if (hide_mode_all)
    {
        $('a').each(function (index, val) {
            let link = $(val).attr('href');
            $(val).attr('href', synpro_hr_getModifiedLink(link));
        });
    } else
    {
        if (hide_mode_post_page)
        {
            $('.entry-content').find('a').each(function (index, val) {
                let link = $(val).attr('href');
                $(val).attr('href', synpro_hr_getModifiedLink(link));
            });
        }
        if (hide_mode_comments)
        {
            $('.comment-content').find('a').each(function (index, val) {
                let link = $(val).attr('href');
                $(val).attr('href', synpro_hr_getModifiedLink(link));
            });
        }
        if (hide_mode_all_comments_admin)
        {
            $('.column-comment').find('a').each(function (index, val) {
                let link = $(val).attr('href');
                $(val).attr('href', synpro_hr_getModifiedLink(link));
            });
        }
    }

});

function synpro_hr_getModifiedLink(link) {
    if (link && link.substr(0, 5).toLowerCase() === 'https' && !synpro_hr_is_exclude_link(link))
    {
        return 'https://'+referrer_link + link;
    }else if (link && link.substr(0, 4).toLowerCase() === 'http' && !synpro_hr_is_exclude_link(link))
    {
        return 'http://'+referrer_link + link;
    }
    return link;
}
function synpro_hr_is_exclude_link(link)
{
    let is_excluded_link = false;
    for (let i = 0; i < exceptions.length; i++)
    {
        item = exceptions[i];
        if (link.indexOf(item) >= 0) {
            is_excluded_link = true;
            break;
        }
    }
    return is_excluded_link;
}