CRM.$('#billing-payment-block').closest('form').submit(function() {
   CRM.api3('PaymentProcessor', 'preapprove', {
     'payment_processor_id': CRM.vars.omnipay.paymentProcessorId,
     'amount': paymentAmount,
     'currencyID' : CRM.vars.omnipay.currency,
     'qf_key': qfKey,
     'is_recur' : isRecur,
     'installments' : $('#installments').val(),
     'frequency_unit' : frequencyUnit,
     'frequency_interval' : frequencyInterval,
     'description' : CRM.vars.omnipay.title + ' ' + CRM.formatMoney(paymentAmount) + recurText,
    }).then(function (result) {
      if (result['is_error'] === 1) {
         reject(result['error_message']);
       }
       else {
         document.getElementById('payment_token').value = result['values'][0];
       }
    })
    .fail(function (result) {
      reject('Payment failed. Check your site credentials');
    });
});

