document.addEventListener('DOMContentLoaded', function () {
    const eventosContainer = document.getElementById('eventos-container');
    const API_URL = '../src/api/eventos.php';

    async function fetchEventos() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) {
                 if (response.status === 404) {
                    eventosContainer.innerHTML = '<p style="color: white; text-align: center;">Nenhum evento agendado no momento.</p>';
                    return;
                }
                throw new Error('Erro de rede ao buscar eventos');
            }
            const eventos = await response.json();
            
            eventosContainer.innerHTML = '';
            if (eventos && eventos.length > 0) {
                eventos.forEach(evento => {
                    const card = createEventoCard(evento);
                    eventosContainer.appendChild(card);
                });
            } else {
                eventosContainer.innerHTML = '<p style="color: white; text-align: center;">Nenhum evento agendado no momento.</p>';
            }
        } catch (error) {
            console.error('Error fetching events:', error);
            eventosContainer.innerHTML = '<p style="color: white; text-align: center;">Ocorreu um erro ao carregar os eventos. Tente novamente mais tarde.</p>';
        }
    }

    function createEventoCard(evento) {
        const card = document.createElement('div');
        card.className = 'evento-card';

        // Formata a data para dd/mm/yyyy
        const data = new Date(evento.data_inicio);
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const ano = data.getFullYear();
        const dataFormatada = `${dia}/${mes}/${ano}`;

        card.innerHTML = `
            <div class="evento-card-banner">
                <span>${evento.nome.split(' ').slice(0, 3).join(' ')}</span>
            </div>
            <div class="evento-card-content">
                <h3>${evento.nome}</h3>
                <p><i class="ph ph-calendar"></i> ${dataFormatada}</p>
                <p><i class="ph ph-map-pin"></i> ${evento.endereco || 'Local a definir'}</p>
                <p><i class="ph ph-info"></i> ${evento.descricao || 'Sem descrição.'}</p>
                <div class="evento-card-actions">
                    <a href="#" class="button">Comprar Ingresso</a>
                </div>
            </div>
        `;
        return card;
    }

    fetchEventos();
});