<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Clients - NJAKA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&family=Orbitron:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #000;
            --neon-pink: #ff006e;
            --neon-cyan: #00fff9;
            --neon-purple: #c800ff;
            --neon-orange: #ff6b00;
            --card: #0d001a;
            --text: #f0f0ff;
            --danger: #ff2b6e;
            --success: #00ff9d;
            --input-bg: #140022;
            --input-border: #3a005a;
            --warning: #e8a547;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Effet de grille en arrière-plan */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: 
                linear-gradient(90deg, rgba(0,255,249,0.03) 1px, transparent 1px),
                linear-gradient(rgba(0,255,249,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 8rem 2rem 3rem 2rem;
            position: relative;
            z-index: 1;
        }

        /* Header */
        header {
            margin-bottom: 4rem;
            position: relative;
            text-align: center;
        }

        header::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,0,110,0.15), transparent 70%);
            pointer-events: none;
            animation: pulse 4s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
        }

        h1 {
            font-family: 'Orbitron', monospace;
            font-size: clamp(3rem, 8vw, 5rem);
            background: linear-gradient(90deg, #fff, var(--neon-cyan), var(--neon-pink), var(--neon-purple), #fff);
            background-size: 400%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: flow 12s linear infinite;
            text-shadow: 0 0 80px rgba(0,255,249,0.5);
            position: relative;
        }

        @keyframes flow { 
            0%{background-position:0% 50%} 
            100%{background-position:400% 50%} 
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Client Cards Grid */
        .clients-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .client-card {
            padding: 2rem;
            background: linear-gradient(145deg, var(--card), rgba(20,0,40,0.8));
            border: 2px solid rgba(0,255,249,0.2);
            transition: all 0.3s ease;
            position: relative;
            border-radius: 20px;
        }

        .client-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: var(--neon-pink);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 20px 0 0 20px;
        }

        .client-card:hover {
            border-color: var(--neon-cyan);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,255,249,0.3);
        }

        .client-card:hover::before {
            transform: scaleY(1);
        }

        .client-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .client-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 20px rgba(0,255,249,0.4);
        }

        .client-info {
            flex: 1;
        }

        .client-name {
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            color: var(--text);
            font-size: 1.3rem;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }

        .client-email {
            color: var(--neon-cyan);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .client-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,255,249,0.2);
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--neon-pink);
            font-weight: 700;
        }

        .detail-value {
            font-size: 1rem;
            color: var(--text);
            font-weight: 500;
        }

        /* Section Réclamations */
        .client-reclamations {
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,255,249,0.2);
        }

        .reclamations-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--neon-cyan);
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .reclamation-item {
            background: rgba(255,0,110,0.05);
            border: 1px solid rgba(255,0,110,0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.8rem;
        }

        .reclamation-item:last-child {
            margin-bottom: 0;
        }

        .reclamation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .reclamation-ref {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--neon-pink);
        }

        .reclamation-date {
            font-size: 0.75rem;
            color: rgba(240,240,255,0.6);
        }

        .reclamation-motif {
            font-size: 0.9rem;
            color: var(--text);
            line-height: 1.4;
        }

        .no-reclamations {
            text-align: center;
            padding: 1.5rem;
            color: rgba(240,240,255,0.4);
            font-size: 0.9rem;
            font-style: italic;
        }

        /* Boutons de statut */
        .status-buttons {
            display: flex;
            gap: 0.8rem;
            grid-column: 1 / -1;
        }

        .status-btn {
            flex: 1;
            padding: 0.9rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: 2px solid;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .status-btn.delivered {
            background: rgba(0,255,157,0.1);
            border-color: var(--success);
            color: var(--success);
        }

        .status-btn.delivered:hover {
            background: var(--success);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,255,157,0.4);
        }

        .status-btn.pending {
            background: rgba(232,165,71,0.1);
            border-color: var(--warning);
            color: var(--warning);
        }

        .status-btn.pending:hover {
            background: var(--warning);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232,165,71,0.4);
        }

        /* Responsive */
        @media (max-width: 968px) {
            .container {
                padding-top: 7rem;
            }

            .clients-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 6rem 1rem 1.5rem 1rem;
            }

            h1 {
                font-size: 2.5rem;
            }

            .clients-grid {
                grid-template-columns: 1fr;
            }

            .client-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <header>
            <h1>GESTION CLIENTS</h1>
        </header>

        <main class="main-content">
            <div class="clients-grid">
                <!-- Client 1 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">JR</div>
                        <div class="client-info">
                            <div class="client-name">Jean Rakoto</div>
                            <div class="client-email">jean.rakoto@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 34 12 345 67</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">12 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">1 250 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">27 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="reclamation-item">
                            <div class="reclamation-header">
                                <span class="reclamation-ref">#REC-001</span>
                                <span class="reclamation-date">20 Jan 2026</span>
                            </div>
                            <div class="reclamation-motif">Produit endommagé lors de la livraison</div>
                        </div>
                    </div>
                </div>

                <!-- Client 2 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">MR</div>
                        <div class="client-info">
                            <div class="client-name">Marie Rabe</div>
                            <div class="client-email">marie.rabe@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 33 98 765 43</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">8 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">890 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">28 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="no-reclamations">Aucune réclamation</div>
                    </div>
                </div>

                <!-- Client 3 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">AR</div>
                        <div class="client-info">
                            <div class="client-name">Andry Ranaivo</div>
                            <div class="client-email">andry.ranaivo@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 32 55 432 10</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">3 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">320 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">15 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="reclamation-item">
                            <div class="reclamation-header">
                                <span class="reclamation-ref">#REC-005</span>
                                <span class="reclamation-date">16 Jan 2026</span>
                            </div>
                            <div class="reclamation-motif">Retard de livraison de 5 jours</div>
                        </div>
                        <div class="reclamation-item">
                            <div class="reclamation-header">
                                <span class="reclamation-ref">#REC-012</span>
                                <span class="reclamation-date">22 Jan 2026</span>
                            </div>
                            <div class="reclamation-motif">Mauvaise taille commandée</div>
                        </div>
                    </div>
                </div>

                <!-- Client 4 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">SR</div>
                        <div class="client-info">
                            <div class="client-name">Sophie Razafy</div>
                            <div class="client-email">sophie.razafy@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 34 78 901 23</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">15 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">2 100 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">29 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="no-reclamations">Aucune réclamation</div>
                    </div>
                </div>

                <!-- Client 5 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">PR</div>
                        <div class="client-info">
                            <div class="client-name">Paul Randriamahatana</div>
                            <div class="client-email">paul.rand@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 33 45 678 90</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">6 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">550 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">25 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="reclamation-item">
                            <div class="reclamation-header">
                                <span class="reclamation-ref">#REC-008</span>
                                <span class="reclamation-date">26 Jan 2026</span>
                            </div>
                            <div class="reclamation-motif">Article manquant dans le colis</div>
                        </div>
                    </div>
                </div>

                <!-- Client 6 -->
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">LR</div>
                        <div class="client-info">
                            <div class="client-name">Lalaina Razafindrakoto</div>
                            <div class="client-email">lalaina.raza@email.mg</div>
                        </div>
                    </div>
                    <div class="client-details">
                        <div class="detail-item">
                            <span class="detail-label">📞 Téléphone</span>
                            <span class="detail-value">+261 32 11 223 34</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📦 Commandes</span>
                            <span class="detail-value">9 commandes</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💰 Total dépensé</span>
                            <span class="detail-value">780 000 Ar</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 Dernière commande</span>
                            <span class="detail-value">30 Jan 2026</span>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn delivered">✅ Livré</button>
                            <button class="status-btn pending">⏳ En cours</button>
                        </div>
                    </div>
                    <div class="client-reclamations">
                        <div class="reclamations-title">⚠️ Réclamations</div>
                        <div class="no-reclamations">Aucune réclamation</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Affichage simple de la liste des clients - lecture seule
    </script>
</body>
</html>