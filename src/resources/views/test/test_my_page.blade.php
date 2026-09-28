<style>
    .container {
        display: flex;
        align-items: center;

        flex-direction: column;
    }

    .sample-container {
        border: 1px solid black;
        padding: 5px;
    }

    .sample-container:not(:last-child) {
        margin-bottom: 10px;
    }

    .card-container {
        display: flex;
    }

    .card:not(:first-child) {
        margin-left: 3px;
    }

    .card {
        width: 170px;
        min-height: 200px;

        border: 1px solid black;

        display: flex;
        align-items: center;

        flex-direction: column;
    }

    .ci-btn-container {
        width: 100%;
    }

    .item-title-input {
        width: 100%;
    }

    .item-container {
        border: 1px solid black;
    }

    .item-container:not(:last-child) {
        margin-bottom: 3px;
    }
</style>

<script type="module">
    import Card from '{{ asset('js/restful/Card.js') }}';
    import Item from '{{ asset('js/restful/Item.js') }}';

    window.Card = new Card('{{ route('home') }}', '{{ csrf_token() }}');
    window.Item = new Item('{{ route('home') }}', '{{ csrf_token() }}');
</script>

<script>
    const createCard = (myPageId) => {
        const cardTitleInput = document.querySelector('input[name="cardTitleInput"]');

        if (cardTitleInput.value === '') {
            return;
        }

        const card = window.Card;
        card.myPageId = myPageId;
        card.title = cardTitleInput.value;

        card.create().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }

    const removeCard = (cardId) => {
        const card = window.Card;
        card.id = cardId;

        card.remove().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }

    const moveCard = (cardId, destinationPosition) => {
        const card = window.Card;
        card.id = cardId;
        //여기서는 무조건 바뀌는 상황이라 조건 검사 안했는데 실제 적용할 때는 포지션 안바뀌면 무시하게 검사해야 함
        card.destinationPosition = destinationPosition;

        card.update().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }

    const createItem = (cardId) => {
        const itemTitleInput = document.querySelector(`input[name="itemTitleInput-${cardId}"]`);

        if (itemTitleInput.value === '') {
            return;
        }

        console.log('test')

        const item = window.Item;
        item.cardId = cardId;
        item.title = itemTitleInput.value;

        item.create().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }

    const removeItem = (itemId) => {
        const item = window.Item;
        item.id = itemId;

        item.remove().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }

    const moveItem = (itemId, destinationPosition, cardId) => {
        const item = window.Item;
        item.id = itemId;
        //여기서는 무조건 바뀌는 상황이라 조건 검사 안했는데 실제 적용할 때는 포지션 안바뀌면 무시하게 검사해야 함
        item.destinationPosition = destinationPosition;
        item.cardId = cardId;

        item.update().then((result) => {
            if (result) {
                // console.log(result);
                //동적으로 요소 추가하는거는 vue에서 하니까 임시로 새로고침
                location.reload();
            }
            else {
                console.log('fail');
            }
        })
    }
</script>

<div class="container">
    <h1>TEST MY PAGE (test page for server testing)</h1>
    <div>
        <h2>Test buttons</h2>
        <div>
            <div class="sample-container">
                <input type="text" name="cardTitleInput" />
                <button onclick="createCard('{{ $myPage->id }}')">create card(flow)</button>
            </div>
{{--            <div class="sample-container">--}}
{{--                <input type="text" name="cardTitleInput" />--}}
{{--                <button>create card(flow)</button>--}}
{{--            </div>--}}
        </div>
    </div>
    <div class="test-card-container">
        <h2>My Page Title: {{ $myPage->title }}, Version: {{ $myPage->version }}</h2>
        <div class="card-container">
            @if ($myPage->cards()->count())
                @foreach($myPage->cardsNum as $cardNum)
                    @foreach ($myPage->cards() as $card)
                        @if ($card->id == $cardNum)
                            <div class="card">
                                <p>Card title: {{ $card->title }}, ID: {{ $card->id }}</p>
                                @if ($card->items()->count())
                                    @foreach($card->itemsNum as $key=>$itemNum)
                                        @foreach($card->items() as $_key=>$item)
                                            @if($item->id == $itemNum)
                                                <div class="item-container">
                                                    <div>Item id: {{ $item->id }}</div>
                                                    <div>Item title: {{ $item->title }}</div>
                                                    <button onclick="removeItem('{{ $item->id }}')">remove item</button>
                                                    <div>
                                                        <button onclick="moveItem('{{ $item->id }}', {{ $_key - 1 }}, '{{ $card->id }}')">up</button>
                                                        <button onclick="moveItem('{{ $item->id }}', {{ $_key + 1 }}, '{{ $card->id }}')">down</button>
                                                    </div>
                                                    <div>
                                                        <button onclick="moveItem('{{ $item->id }}', 0, 7)">1</button>
                                                        <button onclick="moveItem('{{ $item->id }}', 0, 9)">2</button>
                                                        <button onclick="moveItem('{{ $item->id }}', 0, 15)">3</button>
                                                        <button onclick="moveItem('{{ $item->id }}', 0, 18)">4</button>
                                                    </div>
                                                </div>
                                                @break
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                                <div class="ci-btn-container">
                                    <input type="text" name="itemTitleInput-{{ $card->id }}" class="item-title-input" />
                                    <button onclick="createItem('{{ $card->id }}')">create item</button>
                                    <button onclick="removeCard('{{ $card->id }}')">remove card</button>
                                    <div>
                                        @foreach ($myPage->cardsNum as $mKey=>$value)
                                            <button onclick="moveCard('{{ $card->id }}', {{ $mKey }})">{{ $mKey + 1 }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
    <footer>
        Item 이동은 지금 고정 id 기반이라 특정 상황에서만 댐
    </footer>
</div>