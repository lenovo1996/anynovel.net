import java.util.Base64;
import javax.crypto.Cipher;
import javax.crypto.spec.SecretKeySpec;
import javax.crypto.spec.IvParameterSpec;
import java.security.PrivateKey;
import java.io.IOException;
import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.security.KeyFactory;
import java.security.NoSuchAlgorithmException;
import java.security.interfaces.RSAPrivateKey;
import java.security.spec.InvalidKeySpecException;
import java.security.spec.PKCS8EncodedKeySpec;

public class hinovel_decrypt {
    public static void main(String[] args) throws Exception {
        String section_path = args[0];
        String encrypt_content_path = section_path + "/encrypt_content.txt";
        String encrypt_key_path = section_path + "/encrypt_key.txt";
        String encrypt_content = readFile(encrypt_content_path, StandardCharsets.UTF_8);
        String encrypt_key = readFile(encrypt_key_path, StandardCharsets.UTF_8);

        System.out.println(start_decrypt(encrypt_content, encrypt_key));
    }

    static String readFile(String path, Charset encoding) throws IOException {
        byte[] encoded = Files.readAllBytes(Paths.get(path));
        return new String(encoded, encoding);
    }

    public static String decrypt(String privateKey, String encryptContent) {
        try {
            byte[] decode = Base64.getDecoder().decode(encryptContent.getBytes());
            String s1 = "2019919anyuekeji";
            byte[] barr = s1.getBytes();
            Cipher instance = Cipher.getInstance("AES/CBC/NoPadding");
            instance.init(2, new SecretKeySpec(privateKey.getBytes(), "AES"), new IvParameterSpec(barr));
            return new String(instance.doFinal(decode));
        } catch (Exception e2) {
            e2.printStackTrace();
            return null;
        }
    }

    public static PrivateKey getPrivate(String base64PrivateKey) throws Exception {
        try {
            return (RSAPrivateKey) KeyFactory.getInstance("RSA").generatePrivate(new PKCS8EncodedKeySpec(Base64.getDecoder().decode(base64PrivateKey)));
        } catch (NoSuchAlgorithmException unused) {
            throw new Exception("NoSuchAlgorithmException");
        } catch (InvalidKeySpecException unused2) {
            throw new Exception("InvalidKeySpecException");
        } catch (NullPointerException unused3) {
            throw new Exception("NullPointerException");
        }
    }

    public static String start_decrypt(String content, String key) {
        byte[] bArr;
        try {
            PrivateKey privateKey = getPrivate("MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAMSBTGntK8TM4sh7hOxdtq3WZCfjjb3iI0rIiD9ilWAA7qeaWsxQI8yySiy6YGVsY4YF/CNNspgRx8VO8EZI42/hrzlcUOB/ATg1m7MlsPYn2HwSRZ6Kjnt2kUiH/HGsck4lhwMdDoqdN+/do+wkNYvF7H4udQbhnr2kpke0vqWPAgMBAAECgYAv0juTZ7mIGkhye8TcdO35HjyfjHw5IqhuEaE+s7Ige/mYZjMEl9guf5EXk3/UDu2ldx1mRglZgrI4LT7CDAj1Cy9hvEVESeb3pMt7473O84237lpAYPpjzrNSqO6pzRuKGwujzkS3RkDcML0J9vflwpc8gbi3+dr+riV3y61SUQJBAOEmFHNl27quvakK1Y+k6DWUjC4Ecj7tRMpM4VGedbE6nqjt9t7nlX4OqHBJILvU1UO9XzpJlgMJvXsTqUQObRkCQQDfbm+yogqCr1Nvq/cKJmY87LUQHpZLKwAMouP9ccuz/jgKZdzxBJlsAxYPa2mLLc2Q84FdOI8+vrmdggXfHlTnAkBMetj7kiAfu/flEi8VSlkuyjUL9KqyQXralV78kK099MGsdJklgtk/Js+ExPJ/m36OMifE7vYsNgTNaBJZceURAkEAghmbPsfuGNSgX+khSy6634TxlXZKC3D5cWI0IXLuq1s/JIbV1R3ZfDR71vSzm1BLX7j6vd5eQqnqCRYZ9yaBRwJBAMnvQfN8tfqyirOgEC75cBORWpddsq1IqUPB0DXt9xhgE/4hBIXnhHskm1BjLFuRZoV279zQrUrx0j9PELNjx/0");

            byte[] decode = Base64.getDecoder().decode(key);
            try {
                Cipher instance = Cipher.getInstance("RSA/ECB/PKCS1Padding");
                instance.init(2, privateKey);
                bArr = instance.doFinal(decode);
            } catch (Exception unused) {
                bArr = null;
            }
            if (bArr == null) {
                return null;
            }
            return decrypt(new String(bArr), content);
        } catch (Exception e2) {
            e2.printStackTrace();
            return null;
        }
    }
}
