Hi there,
<br/><br/>
@if($card->getOrderType() == 'Self-Print')
Thanks for your gift card purchase! Here’s your order record:

Order placed on: {{ $card->getDate()->format('d-m-Y') }} <br/>
Gift card amount: {{ $card->getCardAmount() }} <br/>
Delivery method: {{ $card->getOrderType() }}     //TODO card Link<br/>
To: {{ $card->getRecipientName() }} <br/>
From: {{ $card->getLender()->getName() }} <br/>
Message: {{ $card->getMessage() }} <br/><br/>
@else
'This gift card was emailed to {{ $card->getRecipientName() }} on {{ $card->getDate()->format('d-m-Y') }}.
@endif