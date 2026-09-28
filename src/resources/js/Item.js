const Item = class {
    createUri = '/createItem';
    removeUri = '/removeItem';
    updateUri = '/updateItem';

    props = {
        id: null,
        cardId : null,
        title : null,
        contents : null,
        images : null,
        comments : null,
        deadline : null,
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

    set contents (data) {
        this.props.contents = data;
    }
    get contents () {
        return this.props.contents;
    }

    set images (data) {
        this.props.images = data;
    }
    get images () {
        return this.props.images;
    }

    set comments (data) {
        this.props.comments = data;
    }
    get comments () {
        return this.props.comments;
    }

    set deadline (data) {
        this.props.deadline = data;
    }
    get deadline () {
        return this.props.deadline;
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
}

export default Item;

// export const test = () => {
//     console.log('test');
// }
