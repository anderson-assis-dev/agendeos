// Adiciona as máscaras ao inputs
const inputCardNumber = document.getElementById("card-number");
const maskOptionsInputCardNumber = {
  mask: "0000 0000 0000 0000",
};

const maskInputCardNumber = IMask(inputCardNumber, maskOptionsInputCardNumber);

const inputDueDate = document.getElementById("due-date");
const maskOptionsInputDueDate = {
  mask: "00/0000",
};

const maskInputDueDate = IMask(inputDueDate, maskOptionsInputDueDate);

const inputCvv = document.getElementById("cvv");
const maskOptionsInputCvv = {
  mask: "000",
};

const maskInputCvv = IMask(inputCvv, maskOptionsInputCvv);

const inputHolderCpf = document.getElementById("holder-cpf");
const inputHolderCpfPix = document.getElementById("holder-cpf-pix");

const maskOptionsInputHolderCpf = {
  mask: "000.000.000-00",
};

const maskInputHolderCpf = IMask(inputHolderCpf, maskOptionsInputHolderCpf);
const maskInputHolderCpfPix = IMask(inputHolderCpfPix, maskOptionsInputHolderCpf);

//

// Adicionar as mensagens de erro nos inputs
inputCardNumber.addEventListener("blur", function () {
  const errorMessage = document.getElementById("error-message-card-number");
  if (inputCardNumber.value.trim() === "") {
    errorMessage.textContent = "Preencha este campo";
  } else {
    errorMessage.textContent = "";
  }
});

const inputHolderName = document.getElementById("holder-name");
inputHolderName.addEventListener("blur", function () {
  const errorMessage = document.getElementById("error-message-holder-name");
  if (inputHolderName.value.trim() === "") {
    errorMessage.textContent = "Preencha este campo";
  } else {
    errorMessage.textContent = "";
  }
});

inputDueDate.addEventListener("blur", function () {
  const errorMessage = document.getElementById("error-message-due-date");
  if (inputHolderName.value.trim() === "") {
    errorMessage.textContent = "Preencha este campo";
  } else {
    errorMessage.textContent = "";
  }
});

inputCvv.addEventListener("blur", function () {
  const errorMessage = document.getElementById("error-message-cvv");
  if (inputHolderName.value.trim() === "") {
    errorMessage.textContent = "Preencha este campo";
  } else {
    errorMessage.textContent = "";
  }
});

inputHolderCpf.addEventListener("blur", function () {
  const errorMessage = document.getElementById("error-message-holder-cpf");
  if (inputHolderCpf.value.trim() === "") {
    errorMessage.textContent = "Preencha este campo";
  } else {
    errorMessage.textContent = "";
  }
});
inputHolderCpfPix.addEventListener("blur", function () {
    const errorMessage = document.getElementById("error-message-holder-cpf-pix");
    if (inputHolderCpfPix.value.trim() === "") {
        errorMessage.textContent = "Preencha este campo";
    } else {
        errorMessage.textContent = "";
    }
});
document.addEventListener("DOMContentLoaded", function() {
    const creditCardFields = document.getElementById("credit-card-fields");
    const pixFields = document.getElementById("pix-fields");
    const dadosPix = document.getElementById("dados-pix");

    creditCardFields.style.display = "block";
    pixFields.style.display = "none";
    document.querySelectorAll('input[name="payment-method"]').forEach((elem) => {
        elem.addEventListener("change", function() {
            if (this.value === "credit-card") {
                creditCardFields.style.display = "block";
                pixFields.style.display = "none";
            } else if (this.value === "pix") {
                creditCardFields.style.display = "none";
                pixFields.style.display = "block";
            }
        });
    });
    const form = document.querySelector('form[name="payment-form"]');
    const paymentMethodInputs = document.querySelectorAll('input[name="payment-method"]');
    const cardNumberInput = document.getElementById("card-number");


    const cvvInput = document.getElementById("cvv");
    const cpfInput = document.getElementById("holder-cpf");
    const nameInput = document.getElementById("holder-name");
    const cpfPixInput = document.getElementById("holder-cpf-pix");
    const submitButton = form.querySelector('.submit-button');
    const overlay = document.getElementById('overlay');

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        submitButton.disabled = true;
        overlay.style.display = 'flex';
        const yearInput = document.getElementById("due-date");
        const [month, year] = yearInput.value.split('/');
        let data = {};
        let paymentMethod = "";
        paymentMethodInputs.forEach((input) => {
            if (input.checked) {
                paymentMethod = input.value;
            }
        });
        let urlParts = window.location.href.split("?");
        urlParts =  urlParts[0].split("/")
        const agendamentoId = urlParts[4];
        if (paymentMethod === "credit-card") {
            const card = PagSeguro.encryptCard({
                publicKey: "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr+ZqgD892U9/HXsa7XqBZUayPquAfh9xx4iwUbTSUAvTlmiXFQNTp0Bvt/5vK2FhMj39qSv1zi2OuBjvW38q1E374nzx6NNBL5JosV0+SDINTlCG0cmigHuBOyWzYmjgca+mtQu4WczCaApNaSuVqgb8u7Bd9GCOL4YJotvV5+81frlSwQXralhwRzGhj/A57CGPgGKiuPT+AOGmykIGEZsSD9RKkyoKIoc0OS8CPIzdBOtTQCIwrLn2FxI83Clcg55W8gkFSOS6rWNbG5qFZWMll6yl02HtunalHmUlRUL66YeGXdMDC2PuRcmZbGO5a/2tbVppW6mfSWG3NPRpgwIDAQAB",
                holder: nameInput.value,
                number: cardNumberInput.value.replace(/\s/g, ''),
                expMonth: month,
                expYear: year,
                securityCode: cvvInput.value
            });
            console.log(card)
            data = {
                agendamento_id: agendamentoId,
                card_number: cardNumberInput.value,
                ano: year,
                mes: month,
                encrypted: card?.encryptedCard,
                cvv: cvvInput.value,
                cpf: cpfInput.value,
                nome: nameInput.value,
                forma_pagamento: 3
            };
        } else if (paymentMethod === "pix") {
            if(!cpfPixInput.value){
                alert("Preencha o campo CPF")
                overlay.style.display = 'none';
                submitButton.disabled = false;

                return false;
            }
            data = {
                agendamento_id: agendamentoId,
                cpf: cpfPixInput.value,
                forma_pagamento: 1
            };
        }
        fetch("http://localhost:8000/pagamento", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro ao processar o pagamento");
                }
                submitButton.disabled = false;
                overlay.style.display = 'none';

                return response.json();
            })
            .then(data => {
                console.log("Resposta da requisição:", data);

                if(paymentMethod === "credit-card"){
                    const dataCard = data?.charges[0]?.status[0];
                    if(dataCard){
                        Swal.fire("Pagamento realizado com sucesso!");
                    }
                }
                else{
                    const imagemPixLink = data?.qr_codes[0]?.links[0]?.href;
                    if(imagemPixLink){
                        //dadosPix.style.display = "block";
                        document.getElementById('pix-image').src = imagemPixLink;
                        Swal.fire({
                            title: "Pague com QR Code ou copia-e-cola",

                            imageUrl: imagemPixLink,
                            text: data?.qr_codes[0]?.text,
                            imageAlt: "QR Code PIX"
                        });
                        const textoPix = data?.qr_codes[0]?.text;
                        document.getElementById('pix-text').value = textoPix;
                    }
                }

                submitButton.disabled = false;
                overlay.style.display = 'none';

            })
            .catch(error => {
                console.log(error)
                submitButton.disabled = false;
                overlay.style.display = 'none';

            });
    });
});
