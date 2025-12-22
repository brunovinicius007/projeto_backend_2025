document.addEventListener('DOMContentLoaded', function() {

    const API_URL = '../src/api/';

    const formLocal = document.getElementById('form-local');
    const localIdInput = document.getElementById('local-id');
    const localEnderecoInput = document.getElementById('local-endereco');
    const localCapacidadeInput = document.getElementById('local-capacidade');
    const listaLocais = document.getElementById('lista-locais');

    async function carregarLocais() {
        try {
            const response = await fetch(`${API_URL}locais.php`);
            if (!response.ok) {
                if (response.status === 404) {
                    listaLocais.innerHTML = '<p>Nenhum local cadastrado.</p>';
                    return;
                }
                throw new Error('Erro ao buscar locais');
            }
            const locais = await response.json();
            
            listaLocais.innerHTML = '';
            
            for (const local of locais) {
                 const card = await criarCard(local, 'local');
                 listaLocais.appendChild(card);
            }

        } catch (error) {
            console.error('Falha ao carregar locais:', error);
            listaLocais.innerHTML = '<p>Erro ao carregar locais.</p>';
        }
    }
    
    formLocal.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = localIdInput.value;
        const url = id ? `${API_URL}locais.php` : `${API_URL}locais.php`;
        const method = id ? 'PUT' : 'POST';

        const body = JSON.stringify({
            id: id,
            endereco: localEnderecoInput.value,
            capacidade_total: localCapacidadeInput.value
        });
        
        try {
            const response = await fetch(url, { method, body, headers: {'Content-Type': 'application/json'} });
            const result = await response.json();
            alert(result.message);
            if(response.ok) {
                formLocal.reset();
                localIdInput.value = '';
                await carregarTudo();
            }
        } catch (error) {
            console.error('Erro ao salvar local:', error);
            alert('Falha ao salvar local.');
        }
    });

    async function criarCard(item, tipo) {
        const card = document.createElement('div');
        card.className = 'item-card';
        let content = '';

        if (tipo === 'local') {
            content = `<h3>${item.endereco}</h3>
                       <p><strong>Capacidade:</strong> ${item.capacidade_total}</p>
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
                    const response = await fetch(`${API_URL}${tipo}is.php?id=${id}`, { method: 'DELETE' });
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
            if (tipo === 'local') {
                localIdInput.value = id;
                localEnderecoInput.value = card.querySelector('h3').textContent;
                localCapacidadeInput.value = card.querySelector('p').textContent.split(': ')[1];
                formLocal.scrollIntoView();
            }
        }
    });

    async function carregarTudo() {
        await carregarLocais();
    }

    carregarTudo();
});