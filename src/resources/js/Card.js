const Card = class {
    createUri = '/createCard';
    removeUri = '/removeCard';
    updateUri = '/updateCard';
    fetchUri = '/fetchCard';

    props = {
        id: null,
        myPageId : null,
        title : null,
        myClassesId: null,
        type: null,
        apply: null,
        btnCardTitle : null,
        itemsNum : null,
        destinationPosition: null,
    };

    set id (data) {
        this.props.id = data;
    }
    get id () {
        return this.props.id;
    }

    set myPageId (data) {
        this.props.myPageId = data;
    }
    get myPageId () {
        return this.props.myPageId;
    }

    set title (data) {
        this.props.title = data;
    }
    get title () {
        return this.props.title;
    }

    set myClassesId (data) {
        this.props.myClassesId = data;
    }
    get myClassesId () {
        return this.props.myClassesId;
    }

    set type (data) {
        this.props.type = data;
    }
    get type () {
        return this.props.type;
    }

    set apply (data) {
        this.props.apply = data;
    }
    get apply () {
        return this.props.apply;
    }

    set btnCardTitle (data) {
        this.props.btnCardTitle = data;
    }
    get btnCardTitle () {
        return this.props.btnCardTitle;
    }

    set itemsNum (data) {
        this.props.itemsNum = data;
    }
    get itemsNum () {
        return this.props.itemsNum;
    }

    set destinationPosition (data) {
        this.props.destinationPosition = data;
    }
    get destinationPosition () {
        return this.props.destinationPosition;
    }

    clear () {
        Object.keys(this.props).forEach((key) => {
            this.props[key] = null;
        })
    }

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

    sendAndPromise (data, type) {
        const formData = this.makeFormData(data);

        const request = new XMLHttpRequest();

        let targetUri = '';
        switch (type) {
            case 'CREATE':
                targetUri = this.createUri;
                break;
            case 'REMOVE':
                targetUri = this.removeUri;
                break;
            case 'UPDATE':
                targetUri = this.updateUri;
                break;
        }
        request.open('POST', this.uri + targetUri, true);
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

    create (data) {
        return this.sendAndPromise(data, 'CREATE');
    }

    remove (data) {
        return this.sendAndPromise(data, 'REMOVE');
    }

    update (data) {
        return this.sendAndPromise(data, 'UPDATE');
    }

    fetch () {
        const request = new XMLHttpRequest();

        let targetUri = this.fetchUri;
        request.open('GET', this.uri + targetUri, true);

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

        request.send(null);

        this.clear();

        return promise;
    }
}

export default Card;

// export const test = () => {
//     console.log('test');
// }
