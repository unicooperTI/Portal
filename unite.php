<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtro de Protocolo com Intervalo de Datas</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .hidden {
            display: none;
        }
        .sub-row {
            background-color: #f9f9f9; /* Cor de fundo diferente para sub-linhas */
        }
        .clickable {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .details-table th, .details-table td {
            border: 1px solid black;
        }
    </style>
</head>
<body>

<h1>Filtro por Regional e Intervalo de Data do Protocolo</h1>

<!-- Campos de filtro -->
<label for="regional">Regional:</label>
<select id="regional">
    <option value="">Todos</option>
    <option value="UNICOOPER">UNICOOPER</option>
    <option value="APPU">APPU</option>
    <option value="UNIÃO">UNIÃO</option>
    <option value="PROTAGON CLUBE DE BENEFICIOS">PROTAGON CLUBE DE BENEFICIOS</option>
    <option value="EXATA">EXATA</option>
</select>

<label for="data_protocolo_de">Data do Protocolo De:</label>
<input type="date" id="data_protocolo_de">

<label for="data_protocolo_ate">Data do Protocolo Até:</label>
<input type="date" id="data_protocolo_ate">

<label for="linhasPorPagina">Linhas por Página:</label>
<select id="linhasPorPagina" onchange="atualizarVisibilidadeLinhas()">
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="1000">1000</option>
</select>

<button onclick="filtrar()">Filtrar</button>
<button onclick="resetar()">Resetar</button>

<!-- Tabela de dados -->
<table id="protocoloTable">
    <thead>
        <tr>
            <th>Protocolo</th>
            <th>Data do Protocolo</th>
            <th>Atendente</th>
            <th>Placa</th>
            <th>KM Total</th>
            <th>Serviço</th>
            <th>Prestador</th>
            <th>Valor do Serviço</th>
            <th>Cliente</th>
            <th>Regional</th>
        </tr>
    </thead>
    <tbody>
        <!-- Linhas serão preenchidas dinamicamente -->
    </tbody>
</table>

<!-- Tabela de resumo de serviços por regional -->
<h2>Resumo de Serviços por Regional - Mês Atual</h2>
<table id="resumoTable">
    <thead>
        <tr>
            <th>Regional</th>
            <th>Número de Serviços</th>
            <th>Soma dos serviços</th>
            <th>Média dos Serviços</th>
        </tr>
    </thead>
    <tbody>
        <!-- Linhas serão preenchidas dinamicamente -->
    </tbody>
</table>

<!-- Tabela de serviços detalhados -->
<h2>Detalhamento dos Serviços - Mês Atual</h2>
<table id="detalhamentoTable">
    <thead>
        <tr>
            <th>Serviço</th>
            <th>Número de Serviços</th>
            <th>Soma dos Serviços</th>
            <th>Média dos Serviços</th>
            <th>Detalhes</th>
        </tr>
    </thead>
    <tbody>
        <!-- Linhas serão preenchidas dinamicamente -->
    </tbody>
</table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script>
    const spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRz132zFHW5Q2SjbDUjRMq8RHyToqIOtG-hWcsTULADwvUcwdERQRDI_g3YMy59aBJMqyw8S2hXjc3O/pub?gid=89029790&single=true&output=csv';

    // Variável para armazenar as linhas da tabela
    let allRows = [];

    // Função para buscar e processar o CSV da planilha usando PapaParse
    async function fetchSheetData() {
        const response = await fetch(spreadsheetUrl);
        const csvText = await response.text();

        // Usa PapaParse para converter CSV em JSON
        return new Promise((resolve) => {
            Papa.parse(csvText, {
                header: true,
                complete: function(results) {
                    resolve(results.data);
                },
                skipEmptyLines: true,
                delimiter: ","
            });
        });
    }

    // Função para carregar os dados na tabela
    async function loadTable() {
        allRows = await fetchSheetData();
        atualizarTabela(allRows);
        atualizarTabelaResumo(allRows);
        atualizarTabelaDetalhamento(allRows);
    }

    // Função para atualizar a tabela com linhas filtradas
    function atualizarTabela(rows) {
        const tableBody = document.getElementById('protocoloTable').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = ""; // Limpa a tabela antes de preencher

        // Preenche a tabela com os dados da planilha
        rows.forEach((row, index) => {
            const newRow = tableBody.insertRow();
            Object.values(row).forEach(value => {
                const newCell = newRow.insertCell();
                newCell.textContent = value;
            });
            // Adiciona uma classe para controlar a visibilidade
            if (index >= 5) newRow.classList.add('hidden');
        });
    }

    // Atualiza a visibilidade das linhas com base na seleção de "Linhas por Página"
    function atualizarVisibilidadeLinhas() {
        const linhasPorPagina = parseInt(document.getElementById('linhasPorPagina').value);
        const rows = document.getElementById('protocoloTable').getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            if (i < linhasPorPagina) {
                rows[i].classList.remove('hidden');
            } else {
                rows[i].classList.add('hidden');
            }
        }
    }

    // Função para filtrar os dados
    function filtrar() {
        let filtroRegional = document.getElementById('regional').value.toLowerCase();
        let filtroDataDe = document.getElementById('data_protocolo_de').value;
        let filtroDataAte = document.getElementById('data_protocolo_ate').value;

        // Filtra as linhas com base nos critérios
        const filteredRows = allRows.filter(row => {
            let exibir = true;

            // Verifica filtro de regional
            if (filtroRegional && row['regional'].toLowerCase() !== filtroRegional) {
                exibir = false;
            }

            // Converte a data da célula para um objeto Date
            let dataProtocolo = new Date(row['data_do_protocolo'].split('/').reverse().join('-')); // Converte 'dd/mm/yyyy' para 'yyyy-mm-dd'

            // Verifica filtro de data "De" e "Até"
            if (filtroDataDe) {
                let dataDe = new Date(filtroDataDe);
                if (dataProtocolo < dataDe) {
                    exibir = false;
                }
            }
            if (filtroDataAte) {
                let dataAte = new Date(filtroDataAte);
                if (dataProtocolo > dataAte) {
                    exibir = false;
                }
            }

            return exibir;
        });

        // Atualiza a tabela com as linhas filtradas
        atualizarTabela(filteredRows);
        atualizarVisibilidadeLinhas(); // Atualiza a visibilidade com base nas linhas filtradas

        // Atualiza a tabela de resumo de serviços após o filtro
        atualizarTabelaResumo(filteredRows);
        atualizarTabelaDetalhamento(filteredRows);
    }

    // Função para resetar os filtros
    function resetar() {
        document.getElementById('regional').value = "";
        document.getElementById('data_protocolo_de').value = "";
        document.getElementById('data_protocolo_ate').value = "";
        filtrar();
    }

    // Função para atualizar a tabela de resumo de serviços
    function atualizarTabelaResumo(rows) {
        const resumoTableBody = document.getElementById('resumoTable').getElementsByTagName('tbody')[0];
        resumoTableBody.innerHTML = ""; // Limpa a tabela antes de preencher

        const servicosPorRegional = {};
        const somaPorRegional = {};
        const contagemPorRegional = {};

        // Filtra as linhas para o mês atual
        const mesAtual = new Date().getMonth() -1;
        const anoAtual = new Date().getFullYear();

        rows.forEach(row => {
            const dataProtocolo = new Date(row['data_do_protocolo'].split('/').reverse().join('-'));
            const mesProtocolo = dataProtocolo.getMonth();
            const anoProtocolo = dataProtocolo.getFullYear();

            if (mesProtocolo === mesAtual && anoProtocolo === anoAtual) {
                const regional = row['regional'];
                const valor = parseFloat(row['valor_servico']) || 0;

                if (!servicosPorRegional[regional]) {
                    servicosPorRegional[regional] = 0;
                    somaPorRegional[regional] = 0;
                    contagemPorRegional[regional] = 0;
                }

                servicosPorRegional[regional] += 1;
                somaPorRegional[regional] += valor;
                contagemPorRegional[regional] += valor > 0 ? 1 : 0;
            }
        });

        // Preenche a tabela de resumo
        for (const regional in servicosPorRegional) {
            const newRow = resumoTableBody.insertRow();
            newRow.insertCell().textContent = regional;
            newRow.insertCell().textContent = servicosPorRegional[regional];
            newRow.insertCell().textContent = somaPorRegional[regional].toFixed(2);
            newRow.insertCell().textContent = (somaPorRegional[regional] / contagemPorRegional[regional]).toFixed(2);
        }
    }

    // Função para atualizar a tabela de detalhamento dos serviços
    function atualizarTabelaDetalhamento(rows) {
        const detalhamentoTableBody = document.getElementById('detalhamentoTable').getElementsByTagName('tbody')[0];
        detalhamentoTableBody.innerHTML = ""; // Limpa a tabela antes de preencher

        const servicosDetalhados = {};
        const somaDetalhada = {};
        const contagemDetalhada = {};

        // Filtra as linhas para o mês atual
        const mesAtual = new Date().getMonth() -1;
        const anoAtual = new Date().getFullYear();

        rows.forEach(row => {
            const dataProtocolo = new Date(row['data_do_protocolo'].split('/').reverse().join('-'));
            const mesProtocolo = dataProtocolo.getMonth();
            const anoProtocolo = dataProtocolo.getFullYear();

            if (mesProtocolo === mesAtual && anoProtocolo === anoAtual) {
                const servico = row['servico'];
                const valor = parseFloat(row['valor_servico']) || 0;

                if (!servicosDetalhados[servico]) {
                    servicosDetalhados[servico] = { count: 0, sum: 0, countNonZero: 0, regionais: {} };
                }

                servicosDetalhados[servico].count += 1;
                servicosDetalhados[servico].sum += valor;
                servicosDetalhados[servico].countNonZero += valor > 0 ? 1 : 0;

                // Adiciona a regional e valor se não estiver presente
                const regional = row['regional'];
                if (!servicosDetalhados[servico].regionais[regional]) {
                    servicosDetalhados[servico].regionais[regional] = { count: 0, sum: 0, countNonZero: 0 };
                }

                servicosDetalhados[servico].regionais[regional].count += 1;
                servicosDetalhados[servico].regionais[regional].sum += valor;
                servicosDetalhados[servico].regionais[regional].countNonZero += valor > 0 ? 1 : 0;
            }
        });

        // Preenche a tabela de detalhamento
        for (const servico in servicosDetalhados) {
            const newRow = detalhamentoTableBody.insertRow();
            newRow.insertCell().textContent = servico;
            newRow.insertCell().textContent = servicosDetalhados[servico].count;
            newRow.insertCell().textContent = servicosDetalhados[servico].sum.toFixed(2);
            newRow.insertCell().textContent = (servicosDetalhados[servico].sum / servicosDetalhados[servico].countNonZero).toFixed(2);
            const detalhesCell = newRow.insertCell();
            detalhesCell.innerHTML = `<span class="clickable" onclick="toggleRegionais('${servico}')">Ver Regionais</span>`;

            // Adiciona uma linha subjacente para mostrar as regionais
            const subRow = detalhamentoTableBody.insertRow();
            subRow.classList.add('hidden', 'sub-row'); // Esconde inicialmente
            const subCell = subRow.insertCell();
            subCell.colSpan = 5; // Mescla as células
            subCell.innerHTML = createRegionaisTable(servicosDetalhados[servico].regionais);
        }
    }

    // Função para criar a tabela de regionais
    function createRegionaisTable(regionais) {
        let html = '<table class="details-table"><thead><tr><th>Regional</th><th>Número de Serviços</th><th>Soma dos Serviços</th><th>Média dos Serviços</th></tr></thead><tbody>';
        
        for (const regional in regionais) {
            html += `<tr>
                        <td>${regional}</td>
                        <td>${regionais[regional].count}</td>
                        <td>${regionais[regional].sum.toFixed(2)}</td>
                        <td>${(regionais[regional].sum / regionais[regional].countNonZero).toFixed(2)}</td>
                     </tr>`;
        }

        html += '</tbody></table>';
        return html;
    }

    
    function toggleRegionais(servico) {
        const rows = document.getElementById('detalhamentoTable').getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            if (row.cells[0].textContent === servico) {
                const subRow = rows[i + 1];
                if (subRow && subRow.classList.contains('sub-row')) {
                    subRow.classList.toggle('hidden');
                }
            }
        }
    }

    
    loadTable();
</script>

</body>
</html>