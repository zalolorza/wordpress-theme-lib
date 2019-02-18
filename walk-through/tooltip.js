import './tooltip.scss';
import Tooltip from 'tooltip.js';
import './offscreen.js';
import Event from './event.js';


class WalkThroughTooltip extends Tooltip {
    
    constructor($target, options, walk) {
        
        
        if(options.scroll==".edit-post-sidebar"){
            
            var $tooltipTarget = $target.parents('.edit-post-sidebar').first();
            options.placement = 'left-start';
            if(typeof options.targetOptions == 'undefined'){
                options.targetOptions = {};
            }
            options.targetOptions.dotPlacement = 'top-left';
        } else if(options.scroll=='.components-popover__content'){

            var $tooltipTarget = $target.parents('.components-popover__content').first();
            options.placement = 'right-start';
            if(typeof options.targetOptions == 'undefined'){
                options.targetOptions = {};
            }
            options.targetOptions.addShadow = false;
            options.targetOptions.dotPlacement = 'top-left';

        } else {
            var $tooltipTarget = $target;
            
        }
       
        super($tooltipTarget,options);
        this.$target = $target;
        this.Walk = walk;


        this.setTooltip();
        this.setTarget();
        this.live = true;
        this.show();
        this.scrollToTarget();
       
      
    }





    setTooltip(){

        this.options.template = this.getTemplate();
        this.options.title = this.options.text;
        this.options.trigger= "click";

        this.options.popperOptions={
            removeOnDestroy: true,
            onCreate: this.onCreateTooltip.bind(this)
       };

    }

    onCreateTooltip(){
                
                var inner = jQuery('.tooltip-inner').html();
                jQuery('.tooltip-inner').html('').append(this.htmlDecode(inner));
            
                jQuery('.close-tooltip').click(this.endWalk.bind(this));

                jQuery('.tooltip-link-callback').click(this.linkCallback.bind(this)); 
                
    }


    getTemplate(){
       return '<div class="tooltip " ><div class="tooltip-body"><div class="tooltip-inner"></div><div class="links">'+this.getLinks()+'</div><button class="close-tooltip"><svg aria-hidden="true" role="img" focusable="false" class="tooltip-close" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20"><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z" stroke="white" stroke-width="1" fill="white"></path></svg></button></div></div>';
    }



    getLinks(){
        var callbackIndex = 0;
        this.callbacks = [];
        var linksHtml = '';



        for (var link of this.options.links) {

            if(typeof link.callback != 'undefined'){
                if(typeof link.callback == 'string'){
                    var tutorialName = link.callback;
                    this.callbacks[callbackIndex] = tutorialName;
                    link.text = (typeof link.text !== 'undefined') ?  link.text : 'Següent tutorial';

                } else if (typeof link.callback == 'function') {
                    link.text = (typeof link.text !== 'undefined') ?  link.text : 'Següent consell';
                    this.callbacks[callbackIndex]= link.callback.bind(this);
                }
                
                const template = '<a class="tooltip-link-callback" data-callback-index="'+callbackIndex+'" >'+ link.text +'</a>';
                callbackIndex++;
                linksHtml += template;
                
            } else {
                if(typeof link.url == 'undefined'){
                    
                    if (this.$target.is('a') && typeof this.$target.attr('href') != 'undefined'){
                        link.url = this.$target.attr('href');
                    } else {
                        link.url = this.$target.find('a').first().attr('href');
                    }
                }
                
                link.text = (typeof link.text !== 'undefined') ?  link.text : 'Ves-hi';
                const template = '<a class="tooltip-link-url" href="'+link.url+'" >'+ link.text +'</a>';
                linksHtml += template;
            }
    
    
          }


          return linksHtml;
    }

    linkCallback(e){
        this.close();
        if(typeof this.callbacks[jQuery(e.target).data('callback-index')] == 'string'){
            var tutorialName = this.callbacks[jQuery(e.target).data('callback-index')];
            this.Walk.fireNewTutorial(tutorialName);
        } else {
            this.callbacks[jQuery(e.target).data('callback-index')].call();
        }
        
    }

    isOpen(){
        return this.live;
    }

    htmlDecode(input){
        var e = document.createElement('div');
        e.innerHTML = input;
        return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
    }

    setTarget(){

        var targetOptions = {
            addShadow : true,
            addDot : true,
            class: '',
            dotPlacement: '',
            dotColor: 'red'
        }

        Object.assign(targetOptions, this.options.targetOptions);
        
        targetOptions.class += ' tooltip-target ';

        if(!targetOptions.addShadow){
            targetOptions.class += ' tooltip-no-shadow ';
        }

        
        this.options.targetOptions = targetOptions;
        
        if(targetOptions.addDot){

            this.addNuxtDot();
        }

        this.$target.addClass(this.options.targetOptions.class);

    }



    addNuxtDot(){
        var nuxtClass = 'nuxt-'+this.options.targetOptions.dotPlacement.trim() +' nuxt-'+this.options.targetOptions.dotColor.trim()+' ';

        if(typeof this.options.targetOptions.addDot == 'string'){
            nuxtClass += ' nuxt-'+this.options.targetOptions.addDot.trim();
        }
        
        this.options.targetOptions.class += ' '+nuxtClass+' ';
        this.$target.prepend('<div class="nux-dot '+nuxtClass+'"></div>');
    }


    killTarget(){
        
        this.$target.removeClass(this.options.targetOptions.class);

        if(typeof this.$scrollParent != 'undefined'){
            this.$scrollParent.removeClass('wt-scroll-parent');
        };

        if(this.options.targetOptions.addDot){
            this.$target.find('.nux-dot').remove();
        }
    }

    close(){
        this.live = false;
        this.killTarget();
        this.dispose();
        
        if( typeof this.options.after != 'undefined'){
            Event.trigger(this.options.after);
        }
    }

    endWalk(){
        this.close();
        this.Walk.end();
    }


    getScrollParent($node) {
        if ($node == null) {
            $node = this.$target;
        }

        if(this.options.scroll){
            if(this.options.scroll == '.components-popover__content'){
                $node = jQuery('.editor-inserter__results');
            } else {
                $node = jQuery(this.options.scroll);
            }
        } 

        if (this.options.scroll || $node == jQuery('body') || $node[0].scrollHeight > $node[0].clientHeight) {

            this.$scrollParent = $node;
            this.originalScroll = this.$scrollParent.scrollTop();
            $node.addClass('wt-scroll-parent');
            
        } else {
          
            this.getScrollParent($node.parent());
        }
      }
    
    scrollToTarget(){

        if(typeof this.options.scroll == 'undefined' || !this.options.scroll) return;

        this.getScrollParent();

        var childPos = this.$target.offset().top + this.$scrollParent.scrollTop();
        var parentPos = this.$scrollParent.parent().offset().top;
        if(this.options.scroll==".edit-post-sidebar"){
            parentPos += 80;
        } else if(this.options.scroll==".edit-post-layout__content"){
            parentPos += 120;
        } else if(this.options.scroll==".components-popover__content"){
            parentPos += 80;
        } 

        var childOffset = childPos - parentPos;

        this.$scrollParent.stop().animate({scrollTop:childOffset}, 500, 'swing');

        

      

    }
     
}

export default WalkThroughTooltip;