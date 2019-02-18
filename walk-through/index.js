import WalkThrough from './walk-through.js';

const WalkThroughSteps = {
    "dashboard" : require('./store/dashboard.json'),
    "edit-case-study" : require('./store/edit-case-study.json'),
    "edit-organization" : require('./store/edit-organization.json'),
    "edit-blog" : require('./store/edit-blog.json'),
    "case-study" : {
            "Menú lateral" : require('./store/sidebar.json'),
            "Opcions" : require('./store/options.json'),
            "Editor Gutenberg" : require('./store/gutenberg.json')
    },
    "organization" : {
        "Menú lateral" : require('./store/sidebar.json'),
        "Opcions" : require('./store/options.json'),
        "Editor Gutenberg" : require('./store/gutenberg.json')
    },
    "blog" : {
        "Opcions generals" : require('./store/sidebar-blog.json'),
        "Editor Gutenberg" : require('./store/gutenberg.json')
    },
    "toplevel_page_notifications-center":require('./store/notifications-center.json')
}

if(typeof WalkThroughSteps[WT_VIEW] != 'undefined'){

    if(typeof WalkThroughSteps[WT_VIEW][0] == 'undefined'){

        jQuery.each( WalkThroughSteps[WT_VIEW], function( name, data ) {
            new WalkThrough(data, name);
        });

    } else {
        new WalkThrough(WalkThroughSteps[WT_VIEW]);
    }
    
}

