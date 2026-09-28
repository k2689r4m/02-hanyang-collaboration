const Rest = class {
    //static varible
    fetchUri = '/fetch';
    types = ['card', 'item'];
    subTypes = ['card', 'myPage', 'classObject', 'team'];

    //member variable
    props = {
        id: null,
        type: null, //card, item
        subType: null, //card, myPage, myClass, team
    };

    //getter setter
    set id (data) {
        this.props.id = data;
    }
    get id () {
        return this.props.id;
    }
    set type (data) {
        if (types.find(data)) {
            this.props.type = data;
        }
    }
    get type () {
        return this.props.type;
    }
    set subType (data) {
        if (subTypes.find(data)) {
            this.props.subType = data;
        }
    }
    get subType () {
        return this.props.subType;
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
                formData.append(key, data[key]);
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

    sendAndPromise (data, type) {
        const formData = this.makeFormData(data);

        const request = new XMLHttpRequest();

        request.open('POST', this.uri + this.fetchUri, true);
        request.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);

        const promise = new Promise((resolve, reject) => {
            request.onload = (e) => {
                if (request.status === 200) {
                    resolve(request.response);
                }
                else {
                    reject(false);
                }
            }
        })

        request.send(formData);

        this.clear();

        return promise;
    }

    fetch (data) {
        return this.sendAndPromise(data);
    }
}

export default Rest;

// export const test = () => {
//     console.log('test');
// }
