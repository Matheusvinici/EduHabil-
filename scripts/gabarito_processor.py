import cv2
import numpy as np
import sys
import json
import os

def detectar_marcacoes(imagem):
    # Converter para escala de cinza
    gray = cv2.cvtColor(imagem, cv2.COLOR_BGR2GRAY)
    
    # Aplicar filtros
    gray = cv2.GaussianBlur(gray, (5, 5), 0)
    gray = cv2.medianBlur(gray, 5)
    
    # Detectar círculos - ajuste estes parâmetros conforme necessário
    circles = cv2.HoughCircles(
        gray, 
        cv2.HOUGH_GRADIENT, 
        dp=1.2, 
        minDist=20,  # Distância mínima entre círculos
        param1=50,   # Limite superior para o detector de bordas
        param2=30,   # Limite para detecção de centro
        minRadius=10,
        maxRadius=30
    )
    
    return circles

def process_gabarito(image_path):
    try:
        # Carregar a imagem
        img = cv2.imread(image_path)
        if img is None:
            return {"error": "Não foi possível carregar a imagem"}
        
        # CONFIGURAÇÕES DO SEU GABARITO (4 alternativas)
        alternativas_por_questao = 4  # A B C D
        margem_esquerda = 30          # Margem esquerda do gabarito
        margem_superior = 30          # Margem superior do gabarito
        espacamento_h = 120           # Espaçamento horizontal entre questões
        espacamento_v = 80            # Espaçamento vertical entre alternativas
        tamanho_circulo = 20          # Tamanho aproximado dos círculos
        
        # Detectar marcações
        circles = detectar_marcacoes(img)
        
        respostas = {}
        if circles is not None:
            circles = np.round(circles[0, :]).astype("int")
            
            # Processar cada círculo detectado
            for (x, y, r) in circles:
                # Determinar qual questão (número)
                questao = int((y - margem_superior) / espacamento_v) + 1
                
                # Determinar qual alternativa (A, B, C, D)
                pos_x_relativa = x - margem_esquerda
                # Verifica se está dentro da área de alternativas
                if 0 <= pos_x_relativa < (alternativas_por_questao * espacamento_h):
                    alternativa_idx = int(pos_x_relativa / espacamento_h)
                    alternativa = chr(65 + alternativa_idx)  # 65 = 'A' em ASCII
                    
                    # Marcar na imagem (para debug)
                    cv2.circle(img, (x, y), r, (0, 255, 0), 4)
                    cv2.putText(img, f"{questao}{alternativa}", (x-10, y+5), 
                                cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 2)
                    
                    # Armazenar resposta (somente se não houver resposta para esta questão)
                    if questao not in respostas:
                        respostas[questao] = {
                            "resposta": alternativa,
                            "confianca": 0.9,  # Valor padrão
                            "x": x,
                            "y": y,
                            "raio": r
                        }
        
        # Salvar imagem processada (para debug)
        output_dir = os.path.dirname(image_path)
        output_filename = "processed_" + os.path.basename(image_path)
        output_path = os.path.join(output_dir, output_filename)
        cv2.imwrite(output_path, img)
        
        return {
            "success": True,
            "respostas": respostas,
            "processed_image": output_path
        }
        
    except Exception as e:
        return {
            "error": str(e),
            "success": False
        }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Caminho da imagem não fornecido"}))
        sys.exit(1)
    
    result = process_gabarito(sys.argv[1])
    print(json.dumps(result))