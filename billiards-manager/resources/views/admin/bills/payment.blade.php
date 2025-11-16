<div class="bill-payment">
  <h2>Thanh toán hóa đơn #{{ $bill->bill_number }}</h2>
  
  <table class="payment-summary">
    <tr>
      <td>Tiền bàn:</td>
      <td>{{ number_format($summary['table_amount']) }}đ</td>
    </tr>
    <tr>
      <td>Tiền đồ ăn/uống:</td>
      <td>{{ number_format($summary['product_amount']) }}đ</td>
    </tr>
    <tr>
      <td>Giảm giá:</td>
      <td>-{{ number_format($summary['discount']) }}đ</td>
    </tr>
    <tr class="total">
      <td><strong>Tổng cộng:</strong></td>
      <td><strong>{{ number_format($summary['total']) }}đ</strong></td>
    </tr>
    <tr class="text-success">
      <td>Đã thanh toán (đặt bàn):</td>
      <td>-{{ number_format($summary['reservation_paid']) }}đ</td>
    </tr>
    <tr class="text-success">
      <td>Đã thanh toán (phát sinh):</td>
      <td>-{{ number_format($summary['additional_paid']) }}đ</td>
    </tr>
    <tr class="remaining">
      <td><strong>Còn phải trả:</strong></td>
      <td><strong class="text-danger">{{ number_format($summary['remaining']) }}đ</strong></td>
    </tr>
  </table>
  
  <form id="paymentForm">
    <input type="hidden" name="amount" value="{{ $summary['remaining'] }}">
    
    <label>Giảm giá thêm (nếu có):</label>
    <input type="number" name="discount_amount" min="0" max="{{ $summary['remaining'] }}">
    
    <div class="payment-methods">
      <button type="button" data-method="cash">Tiền mặt</button>
      <button type="button" data-method="card">Thẻ</button>
      <button type="button" data-method="vnpay">VNPay</button>
    </div>
  </form>
</div>

<script>
  $('[data-method]').click(function() {
    const method = $(this).data('method');
    const form = $('#paymentForm');
    
    $.post('/bills/{{ $bill->id }}/payment/process', {
      ...form.serializeObject(),
      payment_method: method
    })
    .done(function(response) {
      if (response.require_redirect) {
        window.location.href = response.payment_url;
      } else if (response.success) {
        alert('Thanh toán thành công!');
        window.location.reload();
      }
    });
  });
</script>