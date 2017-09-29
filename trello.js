var Trello = require('node-trello')
	,EventEmitter = require('events').EventEmitter
	,extend = require('extend')
	,config,trello,timer,e;

module.exports = function(options){
	var defaults = {
		pollFrequency: 1000*60
		,minId: 0
		,trello: {
			key: ''
			,token: ''
			,boards: []
			// boards: ['WQtuArbW', ...]
		}
		,start: true
	};
	e = new EventEmitter();
	config = extend(true, defaults, options);
	trello = new Trello(config.trello.key, config.trello.token);
	if (config.start){
		process.nextTick(function(){
			start(config.pollFrequency, true);
		});
	}

	var self = {
		on: function(event, listener){
			e.on(event, listener);
			return self;
		}
		,start: function(frequency, immediate){
			start(frequency, immediate);
			return self;
		}
		,stop: function(){
			stop();
			return self;
		}
		,api: trello
	};
	return self;
};

//=================================================

function start(frequency, immediate){
	if (timer) { return; }
	frequency = frequency || config.pollFrequency;
	timer = setInterval(poll, frequency);
	if (immediate){
		poll();
	}
}
function stop(){
	clearInterval(timer);
	timer = null;
}

function poll(){
	config.trello.boards.forEach(function(boardId){
		getBoardActivity(boardId);
	});
}

function getBoardActivity(boardId){
	trello.get('/1/boards/' + boardId + '/actions', function(err, resp){
		if (err) {
			return e.emit('trelloError', err);
		}
		var boardActions = resp.reverse();
		var actionId;
		for (var ix in boardActions){

			//skip seen events
			actionId = parseInt(boardActions[ix].id, 16);
			if (actionId <= config.minId){
				continue;
			}

			var eventType = boardActions[ix].type;
			e.emit(eventType, boardActions[ix], boardId);
		}

		config.minId = Math.max(config.minId, actionId);
		e.emit('maxId', config.minId);
	});
}

function testTrello(){
    var Trello = require('trello-events');
    var trello = new Trello({
        pollFrequency: 1000*60
        ,minId: 0
        ,start: true
        ,trello: {
            boards: ['ZyyzlHyY']
            ,key: '65736934d54dd845b098ebfb9032af75'
            ,token: '927c930f423b3c7f7f2b1fa2d6aa2a7388a7653fc7d50a32d3ceeff9c78fa62a'
        }
    });
    trello.on('updateCard', function(event, boardId){
    	console.log(event);
	});
}
testTrello();