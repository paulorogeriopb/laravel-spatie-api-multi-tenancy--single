const { execSync } = require("child_process");

try {
    // Verifica se há mudanças não commitadas
    const status = execSync("git status --porcelain").toString().trim();
    if (status) {
        console.error(
            "\x1b[31mErro: Você tem mudanças não commitadas. Faça commit ou stash antes de criar a release.\x1b[0m"
        );
        process.exit(1);
    }

    // Captura argumento (patch, minor, major ou versão ex: 2.1.0)
    const releaseArg = process.argv[2] || "patch";

    // Captura a última mensagem de commit para o changelog
    const lastMsg = execSync("git log -1 --pretty=%s").toString().trim();

    console.log(`Criando release: ${releaseArg} com mensagem: "${lastMsg}"`);

    execSync(
        `npx standard-version --release-as ${releaseArg} -m "chore(release): %s - ${lastMsg}"`,
        { stdio: "inherit" }
    );

    console.log("\x1b[32m✔ Release criada com sucesso!\x1b[0m");
} catch (error) {
    console.error("\x1b[31m✖ Erro ao gerar a release:\x1b[0m", error.message);
    process.exit(1);
}
