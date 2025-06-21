
document.getElementById('telefone').addEventListener('input', function (e) {
  // Remove qualquer caractere que não seja número e limita a 11 dígitos
  this.value = this.value.replace(/\D/g, '').slice(0, 11);
});

// Impede a digitação de qualquer caractere que não seja número
document.getElementById('telefone').addEventListener('keypress', function (e) {
  if (!/[0-9]/.test(e.key)) {
    e.preventDefault();
  }
});
// Impede a digitação de qualquer caractere que não seja número

// animação para ocultar dados da table telamedico.html
document.querySelector('.toggle-btn').addEventListener('click', function() {
  const box = document.getElementById('patientsBox');
  box.classList.toggle('hide');
});
// limita a quantidade de caracteres exibidos na tabela
 document.querySelectorAll('input[type="number"][maxlength]').forEach(function(input) {
        input.addEventListener('input', function() {
          if (this.value.length > 3) {
            this.value = this.value.slice(0, 3);
          }
        });
      });

      // Preenche o input com a data e hora atual do computador no formato correto
  window.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('datahora-atual');
    if (input) {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const local = now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());
      input.value = local;
    }
  });
  // Preenche o input de data/hora com o horário atual do dispositivo e impede edição
  document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('datahora-atual');
    if (input) {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const local = now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());
      input.value = local;
    }
  });