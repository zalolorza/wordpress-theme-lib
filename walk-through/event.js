

class Event {

    constructor(){
        this.eventDelay = 50;
        this.reset();
    }

    reset(){
        this.events = [];
        this.delay = 0;
    }

    static trigger(event, delay = 50, callback){


        if(event.event == 'click'){
            jQuery(event.target).click();
        }

        if(typeof callback == 'function'){
            setTimeout(callback,delay);
        }

        return delay;

    }

    addEvent(event){
        this.events.push(event);
        return this;
    }

    addEvents(events){
        this.events = events;
        return this;
    }

    triggerAll(){
        var length = this.events.length;

        if(!length){
            this.reset();
            return 0;
        }
        
        var event = this.events[0];
        this.events.shift();
        if(typeof event.delayAfter == 'undefined'){
            event.delayAfter = this.eventDelay;
        }

        Event.trigger(event,event.delayAfter,this.triggerAll.bind(this));
        return length * this.eventDelay;
    }

}

export default Event;