{{--<style>--}}
{{--    .speech-bubble {--}}
{{--        position: relative;--}}
{{--        background: #a5cccf;--}}
{{--        border-radius: .4em;--}}
{{--    }--}}

{{--    .speech-bubble:after {--}}
{{--        content: '';--}}
{{--        position: absolute;--}}
{{--        top: 0;--}}
{{--        left: 50%;--}}
{{--        width: 0;--}}
{{--        height: 0;--}}
{{--        border: 12px solid transparent;--}}
{{--        border-bottom-color: #a5cccf;--}}
{{--        border-top: 0;--}}
{{--        border-left: 0;--}}
{{--        margin-left: -6px;--}}
{{--        margin-top: -12px;--}}
{{--    }--}}
{{--</style>--}}

{{--<script>--}}
{{--    window.onload = () => {--}}
{{--        // const test = document.getElementById('test');--}}
{{--        //--}}
{{--        // function caretPos(el)--}}
{{--        // {--}}
{{--        //     var pos = 0;--}}
{{--        //     // IE Support--}}
{{--        //     if (document.selection)--}}
{{--        //     {--}}
{{--        //         el.focus ();--}}
{{--        //         var Sel = document.selection.createRange();--}}
{{--        //         var SelLength = document.selection.createRange().text.length;--}}
{{--        //         Sel.moveStart ('character', -el.value.length);--}}
{{--        //         pos = Sel.text.length - SelLength;--}}
{{--        //     }--}}
{{--        //     // Firefox support--}}
{{--        //     else if (el.selectionStart || el.selectionStart == '0')--}}
{{--        //         pos = el.selectionStart;--}}
{{--        //--}}
{{--        //     return pos;--}}
{{--        //--}}
{{--        // }--}}
{{--        //--}}
{{--        // test.oninput = (e) => {--}}
{{--        //     // var position = window.getSelection().getRangeAt(0).startOffset;--}}
{{--        //     // console.log(position);--}}
{{--        //     console.log(caretPos(e.target));--}}
{{--        //     console.log(getComputedStyle(e.target));--}}
{{--        // }--}}


{{--        // function toggleMirrorDivDisplay(checkbox) {--}}
{{--        //     showMirrorDiv = checkbox.checked;--}}
{{--        // }--}}
{{--        ['textarea'].forEach(function (selector) {--}}

{{--            var element = document.querySelector(selector);--}}
{{--            var fontSize = getComputedStyle(element).getPropertyValue('font-size');--}}

{{--            var rect = document.createElement('div');--}}
{{--            document.body.appendChild(rect);--}}
{{--            rect.style.position = 'absolute';--}}
{{--            rect.style.backgroundColor = 'red';--}}
{{--            rect.style.height = fontSize;--}}
{{--            rect.style.width = '1px';--}}

{{--            ['keyup', 'click', 'scroll'].forEach(function (event) {--}}
{{--                element.addEventListener(event, update);--}}
{{--            });--}}

{{--            function update() {--}}
{{--                var coordinates = getCaretCoordinates(element, element.selectionEnd);--}}
{{--                console.log('(top, left) = (%s, %s)', coordinates.top, coordinates.left);--}}
{{--                rect.style.top = element.offsetTop--}}
{{--                    - element.scrollTop--}}
{{--                    + coordinates.top--}}
{{--                    + 'px';--}}
{{--                rect.style.left = element.offsetLeft--}}
{{--                    - element.scrollLeft--}}
{{--                    + coordinates.left--}}
{{--                    + 'px';--}}

{{--                const testTooltip = document.getElementById('test_tooltip');--}}
{{--                testTooltip.style.top = coordinates.top + 35;--}}
{{--                testTooltip.style.left = coordinates.left - 60;--}}
{{--            }--}}
{{--        });--}}

{{--        // The properties that we copy into a mirrored div.--}}
{{--// Note that some browsers, such as Firefox,--}}
{{--// do not concatenate properties, i.e. padding-top, bottom etc. -> padding,--}}
{{--// so we have to do every single property specifically.--}}
{{--        var properties = [--}}
{{--            'boxSizing',--}}
{{--            'width',  // on Chrome and IE, exclude the scrollbar, so the mirror div wraps exactly as the textarea does--}}
{{--            'height',--}}
{{--            'overflowX',--}}
{{--            'overflowY',  // copy the scrollbar for IE--}}

{{--            'borderTopWidth',--}}
{{--            'borderRightWidth',--}}
{{--            'borderBottomWidth',--}}
{{--            'borderLeftWidth',--}}

{{--            'paddingTop',--}}
{{--            'paddingRight',--}}
{{--            'paddingBottom',--}}
{{--            'paddingLeft',--}}

{{--            // https://developer.mozilla.org/en-US/docs/Web/CSS/font--}}
{{--            'fontStyle',--}}
{{--            'fontVariant',--}}
{{--            'fontWeight',--}}
{{--            'fontStretch',--}}
{{--            'fontSize',--}}
{{--            'lineHeight',--}}
{{--            'fontFamily',--}}

{{--            'textAlign',--}}
{{--            'textTransform',--}}
{{--            'textIndent',--}}
{{--            'textDecoration',  // might not make a difference, but better be safe--}}

{{--            'letterSpacing',--}}
{{--            'wordSpacing'--}}
{{--        ];--}}

{{--        var isFirefox = !(window.mozInnerScreenX == null);--}}
{{--        var mirrorDivDisplayCheckbox = document.getElementById('mirrorDivDisplay');--}}
{{--        var mirrorDiv, computed, style;--}}

{{--        getCaretCoordinates = function (element, position) {--}}
{{--            // mirrored div--}}
{{--            mirrorDiv = document.getElementById(element.nodeName + '--mirror-div');--}}
{{--            if (!mirrorDiv) {--}}
{{--                mirrorDiv = document.createElement('div');--}}
{{--                mirrorDiv.id = element.nodeName + '--mirror-div';--}}
{{--                document.body.appendChild(mirrorDiv);--}}
{{--            }--}}

{{--            style = mirrorDiv.style;--}}
{{--            computed = getComputedStyle(element);--}}

{{--            // default textarea styles--}}
{{--            style.whiteSpace = 'pre-wrap';--}}
{{--            if (element.nodeName !== 'INPUT')--}}
{{--                style.wordWrap = 'break-word';  // only for textarea-s--}}

{{--            // position off-screen--}}
{{--            style.position = 'absolute';  // required to return coordinates properly--}}
{{--            style.top = element.offsetTop + parseInt(computed.borderTopWidth) + 'px';--}}
{{--            style.left = "400px";--}}
{{--            // style.visibility = mirrorDivDisplayCheckbox.checked ? 'visible' : 'hidden';  // not 'display: none' because we want rendering--}}
{{--            style.visibility = 'hidden';  // not 'display: none' because we want rendering--}}

{{--            // transfer the element's properties to the div--}}
{{--            properties.forEach(function (prop) {--}}
{{--                style[prop] = computed[prop];--}}
{{--            });--}}

{{--            if (isFirefox) {--}}
{{--                style.width = parseInt(computed.width) - 2 + 'px'  // Firefox adds 2 pixels to the padding - https://bugzilla.mozilla.org/show_bug.cgi?id=753662--}}
{{--                // Firefox lies about the overflow property for textareas: https://bugzilla.mozilla.org/show_bug.cgi?id=984275--}}
{{--                if (element.scrollHeight > parseInt(computed.height))--}}
{{--                    style.overflowY = 'scroll';--}}
{{--            } else {--}}
{{--                style.overflow = 'hidden';  // for Chrome to not render a scrollbar; IE keeps overflowY = 'scroll'--}}
{{--            }--}}

{{--            mirrorDiv.textContent = element.value.substring(0, position);--}}
{{--            // the second special handling for input type="text" vs textarea: spaces need to be replaced with non-breaking spaces - http://stackoverflow.com/a/13402035/1269037--}}
{{--            if (element.nodeName === 'INPUT')--}}
{{--                mirrorDiv.textContent = mirrorDiv.textContent.replace(/\s/g, "\u00a0");--}}

{{--            var span = document.createElement('span');--}}
{{--            // Wrapping must be replicated *exactly*, including when a long word gets--}}
{{--            // onto the next line, with whitespace at the end of the line before (#7).--}}
{{--            // The  *only* reliable way to do that is to copy the *entire* rest of the--}}
{{--            // textarea's content into the <span> created at the caret position.--}}
{{--            // for inputs, just '.' would be enough, but why bother?--}}
{{--            span.textContent = element.value.substring(position) || '.';  // || because a completely empty faux span doesn't render at all--}}
{{--            span.style.backgroundColor = "lightgrey";--}}
{{--            mirrorDiv.appendChild(span);--}}

{{--            var coordinates = {--}}
{{--                top: span.offsetTop + parseInt(computed['borderTopWidth']),--}}
{{--                left: span.offsetLeft + parseInt(computed['borderLeftWidth'])--}}
{{--            };--}}

{{--            return coordinates;--}}
{{--        }--}}
{{--    }--}}
{{--</script>--}}

{{--<textarea id="test" style="width: 100%; height: 100%">--}}
{{--</textarea>--}}
{{--<div class="speech-bubble" id="test_tooltip" style="position: absolute;font-size: 13px; padding: 3px 9px 9px 9px">--}}
{{--    너 지금 여깃음 ㅎㅎ ^^--}}
{{--</div>--}}
{{--<label>--}}
{{--    <input type="checkbox" id="mirrorDivDisplay" onchange="toggleMirrorDivDisplay(this)">Show mirror div--}}
{{--</label>--}}

{{--<script type="module">--}}
{{--    import Card from '{{ asset('js/restful/Card.js') }}';--}}

{{--    window.Card = new Card('{{ route('home') }}', '{{ csrf_token() }}');--}}

{{--    let element = document.querySelector('#uploadCard');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Card.create(--}}
{{--            {--}}
{{--                myPageId: '888',//구현 전 테스트용 마이페이지 아이디--}}
{{--                title: 'test123'--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}

{{--    element = document.querySelector('#removeCard');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Card.remove(--}}
{{--            {--}}
{{--                cardId: '999'//구현 전 테스트용 카드 아이드--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}

{{--    element = document.querySelector('#updateCard');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Card.update(--}}
{{--            {--}}
{{--                cardId: '999'//구현 전 테스트용 카드 아이드--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}


{{--    import Item from '{{ asset('js/restful/Fetch.js') }}';--}}

{{--    window.Fetch = new Fetch('{{ route('home') }}', '{{ csrf_token() }}');--}}

{{--    element = document.querySelector('#uploadItem');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Item.cardId = '1111';--}}
{{--        window.Item.title = 'test';--}}

{{--        window.Item.create().then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}

{{--    element = document.querySelector('#removeItem');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Item.remove(--}}
{{--            {--}}
{{--                test: '2'//구현 전 테스트용 카드 아이드--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}

{{--    element = document.querySelector('#updateItem');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Item.update(--}}
{{--            {--}}
{{--                test: '3'//구현 전 테스트용 카드 아이드--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    console.log(result);--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}
{{--</script>--}}

{{--<div>--}}
{{--    <button id="uploadCard">upload Card test</button>--}}
{{--    <button id="removeCard">remove Card test</button>--}}
{{--    <button id="updateCard">update Card test</button>--}}
{{--</div>--}}
{{--<div>--}}
{{--    <button id="uploadItem">upload Item test</button>--}}
{{--    <button id="removeItem">remove Item test</button>--}}
{{--    <button id="updateItem">update Item test</button>--}}
{{--</div>--}}



{{--<script type="module">--}}
{{--    import Rest from '{{ asset('js/restful/Rest.js') }}';--}}

{{--    window.Rest = new Rest('{{ route('home') }}', '{{ csrf_token() }}');--}}

{{--    let element = document.querySelector('#testFetch');--}}

{{--    element.addEventListener('click', () => {--}}
{{--        window.Rest.fetch(--}}
{{--            {--}}
{{--                id: 21,//구현 전 테스트용 마이페이지 아이디--}}
{{--                types: 'chat',--}}
{{--                subTypes: 'classObject'--}}
{{--            }--}}
{{--        ).then(--}}
{{--            (result) => {--}}
{{--                if (result) {--}}
{{--                    const _result = JSON.parse(result);--}}
{{--                    console.log(_result);--}}

{{--                    if (_result.hasOwnProperty('fail')) {--}}
{{--                        alert(_result.fail);--}}
{{--                    }--}}
{{--                }--}}
{{--                else {--}}
{{--                    console.log('fail');--}}
{{--                }--}}
{{--            }--}}
{{--        )--}}
{{--    });--}}
{{--</script>--}}

{{--<div>--}}
{{--    <button id="testFetch">test fetch</button>--}}
{{--</div>--}}

<script>
    window.onload = () => {
        let a = 15;
        let b = -3;

        console.log(a);
        console.log(b);
    }
</script>
