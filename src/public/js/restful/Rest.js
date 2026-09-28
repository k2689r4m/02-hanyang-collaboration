const Rest = class {
    //static varible
    fetchUri = '/fetch';
    createUri = '/create';
    classSettingUri = '/classSetting';
    deleteUri = '/delete';
    updateUri = '/update';
    chatUri = '/chat';
    downloadUri = '/download';
    brainUri = '/brain';

    types = ['card', 'item', 'setting'];
    subTypes = ['id', 'myPage', 'classObject', 'team', 'card'];

    /**
     * !in fetch method
     * type: card
     * subType: id, myPage, classObject, team
     *
     * type: item
     * subType: id, ...
     *
     *
     * !in post method
     *
     * @type {{subType: null, id: null, type: null}}
     */



    //member variable
    props = {
        id: null,
        types: null,
        subTypes: null,
    };

    //getter setter
    set id (data) {
        this.props.id = data;
    }
    get id () {
        return this.props.id;
    }
    set types (data) {
        if (types.find(data)) {
            this.props.types = data;
        }
    }
    get types () {
        return this.props.types;
    }
    set subTypes (data) {
        if (subTypes.find(data)) {
            this.props.subTypes = data;
        }
    }
    get subTypes () {
        return this.props.subTypes;
    }

    //member function : methods
    clear () {
        Object.keys(this.props).forEach((key) => {
            this.props[key] = null;
        })
    }

    //constructor
    constructor(uri, csrfToken) {
        this.uri = uri;
        this.csrfToken = csrfToken;
    }

    makeFormData (data) {
        if (data) {
            const formData = new FormData();

            Object.keys(data).forEach((key) => {
                if (data[key] === null) {
                    formData.append(key, '');
                }
                else if (key === 'images') {
                    if (data[key]) {
                        data[key].forEach((image) => {
                            formData.append('images[]', image);
                        })
                    }
                }
                else if (key === 'files') {
                    if (data[key]) {
                        data[key].forEach((file) => {
                            formData.append('files[]', file);
                        })
                    }
                }
                else if (key === 'applyFiles') {
                    if (data[key]) {
                        data[key].forEach((file) => {
                            formData.append('applyFiles[]', file);
                        })
                    }
                }
                else if (key === 'imagesNum') {
                    if (data[key]) {
                        data[key].forEach((num) => {
                            formData.append('imagesNum[]', num);
                        })
                    }
                }
                else if (key === 'filesNum') {
                    if (data[key]) {
                        data[key].forEach((num) => {
                            formData.append('filesNum[]', num);
                        })
                    }
                }
                else if (key === 'cardsNum') {
                    data[key].forEach((value) => {
                        formData.append('cardsNum[]', value);
                    })
                }
                else if (key === 'party') {
                    if (data[key]) {
                        formData.append('party', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'checks') {
                    if (data[key]) {
                        formData.append('checks', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'brain') {
                    if (data['type'] === 3) {
                        if (data[key]) {
                            formData.append('brain', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'brains') {
                    if (data['type'] === 3) {
                        if (data[key]) {
                            formData.append('brains', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'problemAnalysis') {
                    if (data['type'] === 1) {
                        if (data[key]) {
                            formData.append('problemAnalysis', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'reflectionLog') {
                    if (data['type'] === 2) {
                        if (data[key]) {
                            formData.append('reflectionLog', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'classApply') {
                    if (data['type'] === 4) {
                        if (data[key]) {
                            formData.append('classApply', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'teamActivity') {
                    if (data['type'] === 5) {
                        if (data[key]) {
                            formData.append('teamActivity', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'operationResult') {
                    if (data['type'] === 8) {
                        if (data[key]) {
                            formData.append('operationResult', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'mentions') {
                    if (data['subTypes'] === 'comment') {
                        if (data[key]) {
                            formData.append('mentions', JSON.stringify(data[key]));
                        }
                    }
                }
                else if (key === 'experts') {
                    if (data[key]) {
                        formData.append('experts', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'classManagers') {
                    if (data[key]) {
                        formData.append('classManagers', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'teamMasters') {
                    if (data[key]) {
                        formData.append('teamMasters', JSON.stringify(data[key]));
                    }
                }
                else if (key.substring(0, 2) === 'on') {
                    if (data[key]) {
                        formData.append(key, '1');
                    }
                    else {
                        formData.append(key, '0');
                    }
                }
                else if (key.substring(0, 10) === 'activation') {
                    if (data[key]) {
                        formData.append(key, '1');
                    }
                    else {
                        formData.append(key, '0');
                    }
                }
                else {
                    formData.append(key, data[key]);
                }
            })

            return formData;
        }
        else {
            const formData = new FormData();

            Object.keys(this.props).forEach((key) => {
                if (this.props[key] != null) {
                    formData.append(key, this.props[key]);
                }
            })

            return formData;
        }
    }

    //XMLHttpRequest()
    //js
    //브라우저

    sendAndPromise (data, action, subUri) {
        const formData = this.makeFormData(data);

        // const request = new XMLHttpRequest();
        //
        // request.open(action, this.uri + subUri, true);
        // request.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);
        //
        // const promise = new Promise((resolve, reject) => {
        //     request.onload = (e) => {
        //         if (request.status === 200) {
        //             resolve(request.response);
        //         }
        //         else {
        //             reject(false);
        //         }
        //     }
        // })


        if(action === 'GET'){
            return axios.get(this.uri + subUri,formData)
        }
        else{
            return axios.post(this.uri + subUri,formData)
        }



        //
        //
        //
        // request.send(formData);
        //
        // this.clear();
        //
        // return promise;
    }

    fetch (data) {
        return this.sendAndPromise(data, 'POST', this.fetchUri);
    }

    classSetting (data) {
        return this.sendAndPromise(data, 'POST', this.classSettingUri);
    }

    create (data) {
        return this.sendAndPromise(data, 'POST', this.createUri);
    }

    delete (data) {
        return this.sendAndPromise(data, 'POST', this.deleteUri);
    }

    update (data) {
        return this.sendAndPromise(data, 'POST', this.updateUri);
    }

    chat (data) {
        return this.sendAndPromise(data, 'POST', this.chatUri);
    }

    download (data) {
        return this.sendAndPromise(data, 'POST', this.downloadUri);
    }

    updateBrain (data) {
        return this.sendAndPromise(data, 'POST', this.brainUri + '/update');
    }

    moveBrain (data) {
        return this.sendAndPromise(data, 'POST', this.brainUri + '/move');
    }
}

export default Rest;

// export const test = () => {
//     console.log('test');
// }
