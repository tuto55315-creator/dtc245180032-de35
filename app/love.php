<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu Nguyễn Hà Giang ❤️</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: radial-gradient(circle at center, #1a0005 0%, #050002 100%);
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Canvas hiệu ứng nền */
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        /* Khối nội dung chính */
        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            user-select: none;
        }

        /* Trái tim 3D đập trung tâm */
        .heart-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px auto;
            cursor: pointer;
            animation: pulse 1.2s infinite ease-in-out;
        }

        .heart {
            background-color: #ff0033;
            display: inline-block;
            height: 100px;
            margin: 0 10px;
            position: relative;
            transform: rotate(-45deg);
            width: 100px;
            box-shadow: 0 0 40px #ff0055, 0 0 80px #ff0033;
        }

        .heart:before,
        .heart:after {
            content: "";
            background-color: #ff0033;
            border-radius: 50%;
            height: 100px;
            position: absolute;
            width: 100px;
            box-shadow: 0 0 40px #ff0055;
        }

        .heart:before {
            top: -50px;
            left: 0;
        }

        .heart:after {
            left: 50px;
            top: 0;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            14% { transform: scale(1.15); }
            28% { transform: scale(1); }
            42% { transform: scale(1.15); }
            70% { transform: scale(1); }
        }

        /* Dòng chữ YÊU NGUYỄN HÀ GIANG màu đỏ rực rỡ */
        .title {
            font-size: 4rem;
            font-weight: 900;
            color: #ff0033;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 
                0 0 10px #ff0033,
                0 0 20px #ff0055,
                0 0 40px #ff0000,
                0 0 80px #b30000;
            animation: glow 2s ease-in-out infinite alternate;
            margin-bottom: 15px;
        }

        .sub-title {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: #ffccd5;
            text-shadow: 0 0 10px rgba(255, 192, 203, 0.8);
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 10px #ff0033, 0 0 20px #ff0055, 0 0 30px #ff0000;
            }
            to {
                text-shadow: 0 0 20px #ff0033, 0 0 40px #ff0055, 0 0 60px #ff0000, 0 0 90px #b30000;
            }
        }

        /* Hướng dẫn nhấp chuột */
        .hint {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: #ff99aa;
            font-size: 0.9rem;
            letter-spacing: 1px;
            opacity: 0.7;
            z-index: 10;
        }
    </style>
</head>
<body>

    <canvas id="canvas"></canvas>

    <div class="container">
        <div class="heart-box" onclick="explodeHearts(event)">
            <div class="heart"></div>
        </div>
        <div class="title">YÊU NGUYỄN HÀ GIANG</div>
        <div class="sub-title">Mãi Mãi Một Tình Yêu ❤️</div>
    </div>

    <div class="hint">✨ Nhấp chuột vào bất kỳ đâu để tạo trái tim bùng nổ ✨</div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        // Mảng chứa các hạt trái tim nền
        const particles = [];
        const heartCount = 60;

        class Particle {
            constructor(x, y, isExplosion = false) {
                this.x = x || Math.random() * width;
                this.y = y || (isExplosion ? y : height + Math.random() * 100);
                this.size = Math.random() * 15 + 8;
                this.speedY = isExplosion ? (Math.random() - 0.5) * 8 : - (Math.random() * 1.5 + 0.5);
                this.speedX = isExplosion ? (Math.random() - 0.5) * 8 : (Math.random() - 0.5) * 1;
                this.opacity = isExplosion ? 1 : Math.random() * 0.7 + 0.3;
                this.fade = isExplosion ? Math.random() * 0.02 + 0.01 : 0;
                this.color = `hsl(${Math.random() * 20 + 340}, 100%, 50%)`;
                this.isExplosion = isExplosion;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.isExplosion) {
                    this.opacity -= this.fade;
                } else {
                    if (this.y < -20) {
                        this.y = height + 20;
                        this.x = Math.random() * width;
                    }
                }
            }

            draw() {
                if (this.opacity <= 0) return;
                ctx.save();
                ctx.globalAlpha = this.opacity;
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.translate(this.x, this.y);
                ctx.scale(this.size / 20, this.size / 20);
                
                // Vẽ hình trái tim
                ctx.moveTo(0, 0);
                ctx.bezierCurveTo(-10, -10, -20, 5, 0, 15);
                ctx.bezierCurveTo(20, 5, 10, -10, 0, 0);
                
                ctx.fill();
                ctx.restore();
            }
        }

        // Khởi tạo các trái tim bay lên ở nền
        for (let i = 0; i < heartCount; i++) {
            particles.push(new Particle());
        }

        // Bùng nổ trái tim khi click chuột
        function explodeHearts(e) {
            const posX = e.clientX || window.innerWidth / 2;
            const posY = e.clientY || window.innerHeight / 2 - 50;

            for (let i = 0; i < 30; i++) {
                particles.push(new Particle(posX, posY, true));
            }
        }

        document.addEventListener('click', explodeHearts);

        // Vòng lặp Render
        function animate() {
            ctx.clearRect(0, 0, width, height);

            for (let i = particles.length - 1; i >= 0; i--) {
                particles[i].update();
                particles[i].draw();

                if (particles[i].isExplosion && particles[i].opacity <= 0) {
                    particles.splice(i, 1);
                }
            }

            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>
</html>
