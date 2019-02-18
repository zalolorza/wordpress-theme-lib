import Tooltip from './tooltip.js';
import Event from './event.js';
import SanitizeTitle from './sanitize-title.js';

class WalkThrough {

    constructor(data, name = "Tutorial"){

        var steps =  Object.assign({}, data);
        this.steps = Object.keys(steps).map(function(key) {
        return steps[key];
        });

       
        
        this.length = data.length;
        this.active = false;
        
        this.tutorialClassName = this.getTutorialClass(name);
        
        var url_string = window.location.href; 
        var url = new URL(url_string);
        var autoStart = decodeURIComponent(url.searchParams.get("start-tutorial"));

        if(autoStart == 'true'){
            autoStart = 'Tutorial';
        }

        jQuery(window).keydown(this.onKeyDown.bind(this));

        if(this.getTutorialClass(autoStart) == this.tutorialClassName){

            setTimeout(this.start.bind(this),600);

        }

        jQuery('#wp-admin-bar-top-secondary').append('<li class="tutorial-button"><a style="cursor:pointer !important" class="ab-item '+this.tutorialClassName+'" data-active="'+name+'" data-inactive="'+name+'"><span class="ab-icon"></span><span class="tutorial-name">'+name+'</span></a></li>');
        this.$tutorialLink = jQuery('.'+this.tutorialClassName);
        
        jQuery('.'+this.tutorialClassName).click(this.toggle.bind(this));

        jQuery( window ).on( "newTutorial",this.onNewTutorial.bind(this) );
        jQuery( window ).on( "fireNewTutorial", this.onFireNewTutorial.bind(this) );
        
    }

    getTutorialClass(name){
        return 'walk-through-link-'+SanitizeTitle(name)
    }

    onNewTutorial(ev, tutorialClassName){
            if(tutorialClassName != this.tutorialClassName) {
                this.end();
            }
    }


    fireNewTutorial(tutorialName){
        this.end();
        jQuery( window ).trigger( "fireNewTutorial", this.getTutorialClass(tutorialName) );
    }

    onFireNewTutorial(ev, tutorialClassName){
        if(tutorialClassName == this.tutorialClassName) {
            this.start();
        }
    }

    toggle(){
        if(this.isActive()) {
            this.end();
        } else {
            this.start();
        }
    }

    start(){
        if(this.isActive()) return;

        jQuery( window ).trigger( "newTutorial", this.tutorialClassName );
        this.$tutorialLink.find('.tutorial-name').html(this.$tutorialLink.data('active'));
        this.$tutorialLink.parent().addClass('is-open');
        this.current = 0;
        this.active = true;
        
        this.next();
    }

    onKeyDown(e) {
            if(!this.isActive()) return;
            if(e.keyCode == 37 || e.keyCode == 38) { 
                e.preventDefault();
                e.stopPropagation();
                this.prev();
            }
            else if(e.keyCode == 39 || e.keyCode == 40) {
                e.preventDefault();
                e.stopPropagation();
                this.next();
            }
    }

    isActive(){
        return this.active;
    }

    setLinks(optionsTooltip){

        optionsTooltip.links = (typeof optionsTooltip.links !== 'undefined') ? optionsTooltip.links : [];

        if(!this.isFirst()){
            optionsTooltip.links.push({text:'Anterior',callback:this.prev.bind(this)});
        }
        if(!this.isLast()){
            optionsTooltip.links.push({callback:this.next.bind(this)});
        } else {
            optionsTooltip.text += "<br><br><b style='color:#3EC67B'>L'enhorabona, ja has acabat aquest tutorial!</b>";
            optionsTooltip.links.push({text:'Tanca',callback:this.end.bind(this)});
        }

        return optionsTooltip;
    }

    tooltip(){
        
        

        var optionsTooltip = this.setLinks(jQuery.extend(true, {}, this.steps[this.current-1]));

        

        if(typeof optionsTooltip.scroll == 'undefined'){
            var $target = jQuery(optionsTooltip.el).first();
            if($target.parents('.edit-post-sidebar').length){
                optionsTooltip.scroll=".edit-post-sidebar";
            } else if($target.parents('.edit-post-layout__content').length){
                optionsTooltip.scroll=".edit-post-layout__content"
            }  else if($target.parents('.components-popover__content').length){
                optionsTooltip.scroll=".components-popover__content"
            }
           
        }

        this.optionsTooltip = optionsTooltip;

        var delay = 0;

        if( typeof optionsTooltip.before != 'undefined'){

    
            if(!Array.isArray(optionsTooltip.before)){
                optionsTooltip.before =[optionsTooltip.before]
            } 
            var events = new Event();

            delay = events.addEvents(optionsTooltip.before).triggerAll();

        }

        if(optionsTooltip.scroll==".edit-post-sidebar"){
            if(typeof optionsTooltip.before == 'undefined'){
                delay = Event.trigger({
                    "event":"click",
                    "target":".edit-post-sidebar__panel-tab:nth-child(1)"
                });
            };
        }

        if(typeof optionsTooltip.delay != 'undefined' && optionsTooltip.delay > delay){
            delay = optionsTooltip.delay;
        }

        setTimeout(this.fireTooltip.bind(this),delay);
       

    }

    fireTooltip(){
        var $target = jQuery(this.optionsTooltip.el).first();
    
        if(!this.targetExists()){
            this.nextIfFails();
            return;
        }
        
        this.currentTooltip = new Tooltip($target, this.optionsTooltip, this);
    }

    prev(){
        if(this.isFirst()) return;

        this.closeCurrent();
        this.current--;

        this.nextIfFails = this.next;

        this.tooltip();
    }

    targetExists(){
       
        
        var $target = jQuery(this.steps[this.current-1].el).first();
      

        if($target.length == 0 || $target.hasClass('acf-hidden') || $target.parents('.acf-hidden').length){

            this.steps.splice(this.current-1, 1);
            this.current--;
            this.length--;
            return false;
        }



        return true;
    }

    next(){

        if(this.isLast()){
            this.end();
            return;
        }

        this.closeCurrent();
        this.current++;

        this.nextIfFails = this.next;

        this.tooltip();
    }

    end(){
        this.active = false;
        this.$tutorialLink.find('.tutorial-name').html(this.$tutorialLink.data('inactive'));
        this.$tutorialLink.parent().removeClass('is-open');
        this.closeCurrent();
    }

    isFirst(){
        return this.current == 1;
    }

    isLast(){
        return this.current >= this.length;
    }

    closeCurrent(){
        if(this.tooltipIsOpen()){
            this.currentTooltip.close();
        }
    }

    tooltipIsOpen(){
        if(typeof this.currentTooltip != 'undefined' && this.currentTooltip.isOpen()){
            return true;
        } else {
            return false;
        }
    }

 

}

export default WalkThrough;