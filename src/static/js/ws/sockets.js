export class SocketDataTransfer{
    constructor(ws, action, data) {
        this.ws = ws;
        this.action = action;
        this.data = data;
    }

    #convert_data(){
        this.data.action = this.action;
        return JSON.stringify(this.data);
    }

    send(){
        let d = this.#convert_data();
        this.ws.send(d);
    }
}