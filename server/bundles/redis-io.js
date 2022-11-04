const RoomIO = require('./room-io');
const AccessIO = require('./access-io');
const logger = require('./logger');
const util = require('util');

class RedisIO {
    constructor(nsp, io, sub, pub, channels) {
        this.nsp = nsp;
        this.io = io;
        this.sub = sub;
        this.pub = pub;
        this.channels = channels;
    }

    /**
     * Get event from data
     * @param data
     */
    parseEvent(data) {
        return Object.assign({name: '', data: {}}, JSON.parse(data));
    };

    /**
     * Init all events on '*'
     * @param socket
     * @return {*}
     */
    wildcard(socket) {
        let Emitter = require('events').EventEmitter;
        let emit = Emitter.prototype.emit;
        let onevent = socket.onevent;
        socket.onevent = function (packet) {
            let args = packet.data || [];
            onevent.call(this, packet);    // original call
            emit.apply(this, ["*"].concat(args));      // additional call to catch-all
        };
        return socket;
    };

    getIoNsp(channel) {
        return channel.replace(this.nsp, '');
    }

    /**
     * on connection
     * @param channel
     * @param data
     */
    on(channel, data) {
        let nsp = this.getIoNsp(channel);
        let nspio = this.io.of('/' + nsp);

        nspio.on('connection', (socket) => {
            socket.roomIO = new RoomIO(socket);
            socket.access = new AccessIO(socket);

            socket = this.wildcard(socket);
            socket.on('*', (name, data) => {
                data = data || {};
                if (true === socket.access.can(name)) {
                    console.log('channel: ' + channel);
                    switch (name) {
                        case 'join' :
                            console.log('    join: ' + data.room);
                            socket.roomIO.join(data.room);
                            this.pub.publish(channel + '.io', JSON.stringify({
                                name: 'room',
                                data: {
                                    type: 'join',
                                    room_id: data.room,
                                    channel: nsp
                                }
                            }));
                            break;
                        case 'leave':
                            console.log('    leave: ' + data.room);
                            socket.roomIO.leave();
                            this.pub.publish(channel + '.io', JSON.stringify({
                                name: 'room',
                                data: {
                                    type: 'leave',
                                    room_id: data.room,
                                    channel: nsp
                                }
                            }));
                            break;
                        default:
                            data.room = socket.roomIO.name();
                            console.log('    ' + name + ': ' + JSON.stringify(data));
                            this.pub.publish(channel + '.io', JSON.stringify({
                                name: name,
                                data: data,
                                channel: nsp
                            }));
							break;
					}
                } else {
                    throw new Error(util.format('Socket %s "can not get access/speed limit", nsp: %s, room: %s, name: %s, data: %s', socket.id, nsp, socket.roomIO.name(), name, JSON.stringify(data)));
                }
            });
            socket.on('disconnect', () => {
                // socket.io disconnect
                console.log('    disconnect: ' + socket.roomIO.name());
                if(socket.roomIO) {
                    this.pub.publish(channel + '.io', JSON.stringify({
                        name: 'room',
                        data: {
                            type: 'disconnect',
                            room_id: socket.roomIO.name(),
                            channel: nsp
                        }
                    }));
                }
            });
        });
    };

    /**
     * Emit event to exist connection
     * @param channel
     * @param data
     */
    emit(channel, data) {
        let event = this.parseEvent(data),
            room = event.data.room,
            nsp = this.getIoNsp(channel);

        if (room) {
            delete event.data.room;
            this.io.of('/' + nsp).to(room).emit(event.name, event.data);
        } else {
            this.io.of('/' + nsp).emit(event.name, event.data);
        }
    };

    /**
     * List redis/socket.io
     */
    listen() {
        for (let i = 0; i < this.channels.length; i++) {
            this.sub.subscribe(this.channels[i]);
            this.on(this.channels[i], JSON.stringify({}));
        }

        this.sub.on("message", (channel, data) => {
            this.emit(channel, data);
        });
    }

}

module.exports = RedisIO;
