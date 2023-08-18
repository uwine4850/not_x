export class LazyLoader{
    #observer;
    #target;
    target_element_id;
    data_fields;
    remove_target_id;
    insert_element_id;
    url;

    /**
     * @param target_element_id (string) ID of the element on which the loading will be triggered.
     * @param data_fields (array) The name of the HTML tag data (data-) to be sent to the server.
     * @param url (string) The URL of the ajax form processing.
     * @param insert_element_id (string) ID of the element where the new html code will be added.
     * @param remove_target_id (string) Removing html id(not tag).
     */
    constructor(target_element_id, data_fields, url, insert_element_id, remove_target_id=false) {
        this.target_element_id = target_element_id;
        this.data_fields = data_fields;
        this.remove_target_id = remove_target_id;
        this.url = url;
        this.insert_element_id = insert_element_id;
        this.#get_target_element_jq();
    }

    /**
     * Start a new instance of the observer and delete the old one.
     * @param on_target (func) The function to be performed.
     */
    #observer_start(on_target) {
        let target = document.getElementById(this.target_element_id);
        if (!target){
            return;
        }
        if (this.#observer){
            this.#observer.disconnect();
        }
        this.#observer = new IntersectionObserver((entries) => {
            entries.forEach( (entry) => {
                if (entry.isIntersecting) {
                    on_target();
                }
            });
        });
        this.#observer.observe(target);
    }

    /**
     * Creates an associative array with data from an html tag.
     */
    #get_element_data() {
        let data = {};
        for (let i = 0; i < this.data_fields.length; i++) {
            data[this.data_fields[i]] = this.#target.data(this.data_fields[i]);
        }
        return data;
    }

    /**
     * @param url (string) Form Processing URL.
     * @param on_success (func) Function to execute when a form is submitted and a response is received.
     */
    #start_ajax(url, on_success) {
        $.ajax({
            url: url,
            method: 'GET',
            data: this.#get_element_data(),
            success: (response) => {
                on_success(response);
            }
        });
    }

    #get_target_element_jq() {
        this.#target = $(`#${this.target_element_id}`);
    }

    /**
     * Starts observation and data loading.
     * @param on_success (func) A function that will be executed after each new data load.
     */
    start(on_success) {
        this.#observer_start(() =>{
            this.#start_ajax(this.url, (response) => {
                $(`#${this.insert_element_id}`).append(response);
                this.#observer.disconnect();
                on_success();

                // Get a new target ID if the old one is deleted. Or not receive if the ID no longer exists.
                this.#get_target_element_jq()

                // Processing of new data.
                this.start(on_success);
            });
        });
        if (this.remove_target_id){
            this.#target.removeAttr("id");
        }
    }
}
