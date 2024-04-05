const { exec } = require('child_process');

// Função para executar um script PHP
function runPhpScript(scriptName) {
    return new Promise((resolve, reject) => {
        const command = `php ${scriptName}`;
        exec(command, (error, stdout, stderr) => {
            if (error) {
                reject(stderr);
            } else {
                resolve(stdout);
            }
        });
    });
}

// Agendar a execução dos scripts PHP
async function schedulePhpScripts() {
    try {
        console.log("Iniciando execução dos scripts PHP...");

        // Execute os scripts PHP
        await runPhpScript('restClientes.php');
        await runPhpScript('restClientesDuplicatas.php');
        await runPhpScript('restFornecedores.php');
        await runPhpScript('restFornecedoresDuplicatas.php');
       

        console.log("Scripts PHP concluídos com sucesso!");
    } catch (error) {
        console.error("Erro durante a execução dos scripts PHP:", error);
    }
}

// Agendar a execução dos scripts a cada 6 horas (4 vezes ao dia)
setInterval(schedulePhpScripts, 6 * 60 * 60 * 1000);

// Executar imediatamente ao iniciar o script
schedulePhpScripts();
