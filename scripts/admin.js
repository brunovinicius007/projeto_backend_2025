document.addEventListener('DOMContentLoaded', function() {

    const API_URL = '../src/api/';

    // --- Elementos dos Formulários ---
    const formEvento = document.getElementById('form-evento');
    const eventoIdInput = document.getElementById('evento-id');
    const eventoNomeInput = document.getElementById('evento-nome');
    const eventoDataInicioInput = document.getElementById('evento-data-inicio');
    const eventoDataFimInput = document.getElementById('evento-data-fim');
    const eventoLocalIdSelect = document.getElementById('evento-local-id');
    const eventoDescricaoInput = document.getElementById('evento-descricao');
    const eventoPoliticaInput = document.getElementById('evento-politica');
    const listaEventos = document.getElementById('lista-eventos');

    const formSetor = document.getElementById('form-setor');
    const setorIdInput = document.getElementById('setor-id');
    const setorEventoIdSelect = document.getElementById('setor-evento-id');
    const setorNomeInput = document.getElementById('setor-nome');
    const setorCapacidadeInput = document.getElementById('setor-capacidade');
    const listaSetores = document.getElementById('lista-setores');

    const formLote = document.getElementById('form-lote');
    const loteIdInput = document.getElementById('lote-id');
    const loteSetorIdSelect = document.getElementById('lote-setor-id');
    const lotePrecoInput = document.getElementById('lote-preco');
    const loteQuantidadeInput = document.getElementById('lote-quantidade');
    const loteVigenciaIniInput = document.getElementById('lote-vigencia-ini');
    const loteVigenciaFimInput = document.getElementById('lote-vigencia-fim');
    const listaLotes = document.getElementById('lista-lotes');

    // --- FUNÇÕES DE CARREGAMENTO INICIAL ---

    async function carregarLocais() {
        try {
            const response = await fetch(`${API_URL}locais.php`);
            if (!response.ok) {
                if (response.status === 404) {
                    eventoLocalIdSelect.innerHTML = '<option>Cadastre um local primeiro</option>';
                    return;
                }
                throw new Error('Erro ao buscar locais');
            }
            const locais = await response.json();
            eventoLocalIdSelect.innerHTML = '<option value="">Selecione um Local</option>';
            locais.forEach(local => {
                const option = document.createElement('option');
                option.value = local.id;
                option.textContent = local.endereco;
                eventoLocalIdSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Falha ao carregar locais:', error);
            eventoLocalIdSelect.innerHTML = '<option>Erro ao carregar locais</option>';
        }
    }


    // --- FUNÇÕES DE EVENTOS ---

    async function carregarEventos() {
        try {
            const response = await fetch(`${API_URL}eventos.php`);
            if (!response.ok) {
                if (response.status === 404) {
                    listaEventos.innerHTML = '<p>Nenhum evento cadastrado.</p>';
                    setorEventoIdSelect.innerHTML = '<option>Cadastre um evento primeiro</option>';
                    return;
                }
                throw new Error('Erro ao buscar eventos');
            }
            const eventos = await response.json();
            
            listaEventos.innerHTML = '';
            setorEventoIdSelect.innerHTML = '<option value="">Selecione um Evento</option>';

            for (const evento of eventos) {
                 const card = await criarCard(evento, 'evento');
                 listaEventos.appendChild(card);

                const option = document.createElement('option');
                option.value = evento.id;
                option.textContent = evento.nome;
                setorEventoIdSelect.appendChild(option);
            }

        } catch (error) {
            console.error('Falha ao carregar eventos:', error);
            listaEventos.innerHTML = '<p>Erro ao carregar eventos.</p>';
        }
    }
    
    formEvento.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = eventoIdInput.value;
        const url = id ? `${API_URL}eventos.php` : `${API_URL}eventos.php`; // PUT não usa ID na URL aqui
        const method = id ? 'PUT' : 'POST';

        const body = JSON.stringify({
            id: id,
            nome: eventoNomeInput.value,
            data_inicio: eventoDataInicioInput.value,
            data_fim: eventoDataFimInput.value,
            local_id: eventoLocalIdSelect.value,
            descricao: eventoDescricaoInput.value,
            politica_cancelamento: eventoPoliticaInput.value
        });
        
        try {
            const response = await fetch(url, { method, body, headers: {'Content-Type': 'application/json'} });
            const result = await response.json();
            alert(result.message);
            if(response.ok) {
                formEvento.reset();
                eventoIdInput.value = '';
                await carregarTudo();
            }
        } catch (error) {
            console.error('Erro ao salvar evento:', error);
            alert('Falha ao salvar evento.');
        }
    });


    // --- FUNÇÕES DE SETORES ---

    async function carregarSetores() {
        try {
            const response = await fetch(`${API_URL}setores.php`);
             if (!response.ok) {
                if (response.status === 404) {
                    listaSetores.innerHTML = '<p>Nenhum setor cadastrado.</p>';
                    loteSetorIdSelect.innerHTML = '<option>Cadastre um setor primeiro</option>';
                    return;
                }
                throw new Error('Erro ao buscar setores');
            }
            const setores = await response.json();

            listaSetores.innerHTML = '';
            loteSetorIdSelect.innerHTML = '<option value="">Selecione um Setor</option>';
            
            for (const setor of setores) {
                const card = await criarCard(setor, 'setor');
                listaSetores.appendChild(card);

                const option = document.createElement('option');
                option.value = setor.id;
                option.textContent = `${setor.nome} (${setor.evento_nome})`;
                loteSetorIdSelect.appendChild(option);
            }
        } catch (error) {
            console.error('Falha ao carregar setores:', error);
            listaSetores.innerHTML = '<p>Erro ao carregar setores.</p>';
        }
    }

    formSetor.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = setorIdInput.value;
        const url = id ? `${API_URL}setores.php` : `${API_URL}setores.php`;
        const method = id ? 'PUT' : 'POST';

        const body = JSON.stringify({
            id: id,
            evento_id: setorEventoIdSelect.value,
            nome: setorNomeInput.value,
            capacidade: setorCapacidadeInput.value
        });

        try {
            const response = await fetch(url, { method, body, headers: {'Content-Type': 'application/json'} });
            const result = await response.json();
            alert(result.message);
            if (response.ok) {
                formSetor.reset();
                setorIdInput.value = '';
                await carregarTudo();
            }
        } catch (error) {
            console.error('Erro ao salvar setor:', error);
            alert('Falha ao salvar setor.');
        }
    });

    // --- FUNÇÕES DE LOTES ---

    async function carregarLotes() {
        try {
            const response = await fetch(`${API_URL}lotes.php`);
            if (!response.ok) {
                if (response.status === 404) {
                    listaLotes.innerHTML = '<p>Nenhum lote cadastrado.</p>';
                    return;
                }
                throw new Error('Erro ao buscar lotes');
            }
            const lotes = await response.json();
            listaLotes.innerHTML = '';
            for (const lote of lotes) {
                const card = await criarCard(lote, 'lote');
                listaLotes.appendChild(card);
            }
        } catch (error) {
            console.error('Falha ao carregar lotes:', error);
            listaLotes.innerHTML = '<p>Erro ao carregar lotes.</p>';
        }
    }

    formLote.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = loteIdInput.value;
        const url = id ? `${API_URL}lotes.php` : `${API_URL}lotes.php`;
        const method = id ? 'PUT' : 'POST';

        const body = JSON.stringify({
            id: id,
            setor_id: loteSetorIdSelect.value,
            preco: lotePrecoInput.value,
            quantidade: loteQuantidadeInput.value,
            periodo_vigencia_ini: loteVigenciaIniInput.value,
            periodo_vigencia_fim: loteVigenciaFimInput.value
        });

        try {
            const response = await fetch(url, { method, body, headers: {'Content-Type': 'application/json'} });
            const result = await response.json();
            alert(result.message);
            if(response.ok) {
                formLote.reset();
                loteIdInput.value = '';
                await carregarTudo();
            }
        } catch (error) {
            console.error('Erro ao salvar lote:', error);
            alert('Falha ao salvar lote.');
        }
    });
    
    // --- FUNÇÕES GERAIS (CARD, DELETE, EDIT) ---

    async function criarCard(item, tipo) {
        const card = document.createElement('div');
        card.className = 'item-card';
        let content = '';

        if (tipo === 'evento') {
            const dataInicio = new Date(item.data_inicio);
            const dataFim = new Date(item.data_fim);
            
            const formatarData = (data) => `${data.toLocaleDateString()} ${data.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            content = `<h3>${item.nome}</h3>
                       <p><strong>Início:</strong> ${formatarData(dataInicio)}</p>
                       <p><strong>Fim:</strong> ${formatarData(dataFim)}</p>
                       <p><strong>Local:</strong> ${item.endereco || 'A definir'}</p>
                       <div class="card-details" style="display:none;">
                            <span class="data-inicio">${item.data_inicio}</span>
                            <span class="data-fim">${item.data_fim}</span>
                            <span class="local-id">${item.local_id}</span>
                            <span class="descricao">${item.descricao}</span>
                            <span class="politica">${item.politica_cancelamento}</span>
                       </div>
                       `;
        } else if (tipo === 'setor') {
            content = `<h3>${item.nome}</h3>
                       <p><strong>Evento:</strong> ${item.evento_nome}</p>
                       <p><strong>Capacidade:</strong> ${item.capacidade}</p>`;
        } else if (tipo === 'lote') {
             content = `<h3>Lote #${item.id}</h3>
                       <p><strong>Setor:</strong> ${item.setor_nome}</p>
                       <p><strong>Preço:</strong> R$ ${parseFloat(item.preco).toFixed(2)}</p>
                       <p><strong>Limite:</strong> ${item.limite}</p>
                       <div class="card-details" style="display:none;">
                            <span class="vigencia-ini">${item.periodo_vigencia_ini}</span>
                            <span class="vigencia-fim">${item.periodo_vigencia_fim}</span>
                       </div>
                       `;
        }

        card.innerHTML = `${content}
            <div class="item-actions">
                <button class="btn-edit" data-id="${item.id}" data-tipo="${tipo}">Editar</button>
                <button class="btn-delete" data-id="${item.id}" data-tipo="${tipo}">Excluir</button>
            </div>`;
        return card;
    }

    document.querySelector('main').addEventListener('click', async function(e) {
        const target = e.target;
        if (!target.dataset.id || !target.dataset.tipo) return;

        const id = target.dataset.id;
        const tipo = target.dataset.tipo;

        if (target.classList.contains('btn-delete')) {
            if (confirm(`Tem certeza que deseja excluir este ${tipo}?`)) {
                try {
                    const response = await fetch(`${API_URL}${tipo}s.php?id=${id}`, { method: 'DELETE' });
                    const result = await response.json();
                    alert(result.message);
                    if(response.ok) {
                       await carregarTudo();
                    }
                } catch (error) {
                    console.error(`Erro ao deletar ${tipo}:`, error);
                    alert(`Falha ao deletar ${tipo}.`);
                }
            }
        }

        if (target.classList.contains('btn-edit')) {
            const card = target.closest('.item-card');
            if (tipo === 'evento') {
                eventoIdInput.value = id;
                eventoNomeInput.value = card.querySelector('h3').textContent;

                eventoDataInicioInput.value = card.querySelector('.data-inicio').textContent;
                eventoDataFimInput.value = card.querySelector('.data-fim').textContent;

                eventoLocalIdSelect.value = card.querySelector('.local-id').textContent;
                eventoDescricaoInput.value = card.querySelector('.descricao').textContent;
                eventoPoliticaInput.value = card.querySelector('.politica').textContent;
                
                formEvento.scrollIntoView();
            } else if (tipo === 'setor') {
                setorIdInput.value = id;
                setorNomeInput.value = card.querySelector('h3').textContent;
                setorCapacidadeInput.value = card.querySelector('p:last-of-type').textContent.split(': ')[1];
                const nomeEvento = card.querySelector('p:nth-of-type(1)').textContent.split(': ')[1];
                const option = Array.from(setorEventoIdSelect.options).find(opt => opt.text === nomeEvento);
                if(option) setorEventoIdSelect.value = option.value;
                formSetor.scrollIntoView();
            } else if (tipo === 'lote') {
                loteIdInput.value = id;
                lotePrecoInput.value = card.querySelector('p:nth-of-type(2)').textContent.split('R$ ')[1];
                loteQuantidadeInput.value = card.querySelector('p:nth-of-type(3)').textContent.split(': ')[1];
                
                loteVigenciaIniInput.value = card.querySelector('.vigencia-ini').textContent;
                loteVigenciaFimInput.value = card.querySelector('.vigencia-fim').textContent;

                const nomeSetor = card.querySelector('p:nth-of-type(1)').textContent.split(': ')[1];
                const option = Array.from(loteSetorIdSelect.options).find(opt => opt.text.startsWith(nomeSetor));
                if(option) loteSetorIdSelect.value = option.value;
                formLote.scrollIntoView();
            }
        }
    });

    async function carregarTudo() {
        await carregarLocais();
        await carregarEventos();
        await carregarSetores();
        await carregarLotes();
    }

    carregarTudo();
});