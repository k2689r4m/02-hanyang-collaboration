const Item = class {
    createUri = '/createItem';
    removeUri = '/removeItem';
    updateUri = '/updateItem';
    fetchUri = '/fetchItem';

    props = {
        id: null,
        cardId : null,
        title : null,
        content : null,
        images : null,
        imagesNum: null,
        comments : null,
        deadLine : null,
        party : null,
        label : null,
        checks : null,
        activationDeadline : null,
        activationImage : null,
        activationParty : null,
        activationLabel : null,
        activationCheck : null,
        brain : null,
        classApply : null,
        detaileOperApply : null,
        problemAnalysis : null,
        teamActivity : null,
        evaluation : null,
        reflectionLog : null,
        operationResult : null,
        destinationPosition: null,
    };

    set id (data) {
        this.props.id = data;
    }
    get id () {
        return this.props.id;
    }

    set cardId (data) {
        this.props.cardId = data;
    }
    get cardId () {
        return this.props.cardId;
    }

    set title (data) {
        this.props.title = data;
    }
    get title () {
        return this.props.title;
    }

    set content (data) {
        this.props.content = data;
    }
    get content () {
        return this.props.content;
    }

    set images (data) {
        this.props.images = data;
    }
    get images () {
        return this.props.images;
    }

    set imagesNum (data) {
        this.props.imagesNum = data;
    }
    get imagesNum () {
        return this.props.imagesNum;
    }

    set comments (data) {
        this.props.comments = data;
    }
    get comments () {
        return this.props.comments;
    }

    set deadLine (data) {
        this.props.deadLine = data;
    }
    get deadLine () {
        return this.props.deadLine;
    }

    set party (data) {
        this.props.party = data;
    }
    get party () {
        return this.props.party;
    }

    set label (data) {
        this.props.label = data;
    }
    get label () {
        return this.props.label;
    }

    set checks (data) {
        this.props.checks = data;
    }
    get checks () {
        return this.props.checks;
    }

    set activationDeadline (data) {
        this.props.activationDeadline = data;
    }
    get activationDeadline () {
        return this.props.activationDeadline;
    }

    set activationImage (data) {
        this.props.activationImage = data;
    }
    get activationImage () {
        return this.props.activationImage;
    }

    set activationParty (data) {
        this.props.activationParty = data;
    }
    get activationParty () {
        return this.props.activationParty;
    }

    set activationLabel (data) {
        this.props.activationLabel = data;
    }
    get activationLabel () {
        return this.props.activationLabel;
    }

    set activationCheck (data) {
        this.props.activationCheck = data;
    }
    get activationCheck () {
        return this.props.activationCheck;
    }

    set brain (data) {
        this.props.brain = data;
    }
    get brain () {
        return this.props.brain;
    }

    set classApply (data) {
        this.props.classApply = data;
    }
    get classApply () {
        return this.props.classApply;
    }

    set detaileOperApply (data) {
        this.props.detaileOperApply = data;
    }
    get detaileOperApply () {
        return this.props.detaileOperApply;
    }

    set problemAnalysis (data) {
        this.props.problemAnalysis = data;
    }
    get problemAnalysis () {
        return this.props.problemAnalysis;
    }

    set teamActivity (data) {
        this.props.teamActivity = data;
    }
    get teamActivity () {
        return this.props.teamActivity;
    }

    set evaluation (data) {
        this.props.evaluation = data;
    }
    get evaluation () {
        return this.props.evaluation;
    }

    set reflectionLog (data) {
        this.props.reflectionLog = data;
    }
    get reflectionLog () {
        return this.props.reflectionLog;
    }

    set operationResult (data) {
        this.props.operationResult = data;
    }
    get operationResult () {
        return this.props.operationResult;
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
                else if (key === 'imagesNum') {
                    if (data[key]) {
                        data[key].forEach((num) => {
                            formData.append('imagesNum[]', num);
                        })
                    }
                }
                else if (key === 'checks') {
                    if (data[key]) {
                        // data[key].forEach((check) => {
                        //     formData.append('checks[]', _formData);//`{content: ${check.content}, checked:${check.checked}}`);
                        // })
                        formData.append('checks', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'brain') {
                    if (data[key]) {
                        // data[key].forEach((check) => {
                        //     formData.append('checks[]', _formData);//`{content: ${check.content}, checked:${check.checked}}`);
                        // })
                        formData.append('brain', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'problemAnalysis') {
                    if (data[key]) {
                        formData.append('problemAnalysis', JSON.stringify(data[key]));
                    }
                }
                else if (key === 'reflectionLog') {
                    if (data[key]) {
                        formData.append('reflectionLog', JSON.stringify(data[key]));
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
                    if (key === 'images') {
                        if (this.props[key]) {
                            this.props[key].forEach((image) => {
                                formData.append('images[]', image);
                            })
                        }
                    }
                    else if (key === 'imagesNum') {
                        if (this.props[key]) {
                            this.props[key].forEach((num) => {
                                formData.append('imagesNum[]', num);
                            })
                        }
                    }
                    else if (key === 'checks') {
                        if (this.props[key]) {
                            // this.props[key].forEach((check) => {
                            //     formData.append('checks[]', check);
                            // })
                            formData.append('checks', JSON.stringify(this.props[key]));
                        }
                    }
                    else if (key === 'brain') {
                        if (this.props[key]) {
                            // this.props[key].forEach((check) => {
                            //     formData.append('checks[]', check);
                            // })
                            formData.append('brain', JSON.stringify(this.props[key]));
                        }
                    }
                    else {
                        formData.append(key, this.props[key]);
                    }
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

    fetch (cardId) {
        const request = new XMLHttpRequest();

        let targetUri = this.fetchUri;
        request.open('GET', this.uri + targetUri + '/' + cardId, true);

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

export default Item;

// export const test = () => {
//     console.log('test');
// }
